<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Generator of rules which and where folders from code base should be copied
 */
class Generator_CopyRule
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_View|null
     */
    private $_fallbackList = null;

    /**
     * @var Generator_ThemeProxy[]
     */
    private $_themes = array();

    /**
     * @var Mage_Core_Model_Dir
     */
    private $_dirs;

    /**
     * Constructor
     *
     * @param string $sourcePath
     */
    public function __construct($sourcePath = '')
    {
        if (empty($sourcePath)) {
            $sourcePath = BP;
        }
        $this->_dirs = new Mage_Core_Model_Dir(
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local),
            $sourcePath
        );

        $this->_initThemes();
        $this->_fallbackList = new Mage_Core_Model_Design_Fallback_List_View($this->_dirs);
    }

    /**
     * Get rules for copying static view files
     * returns array(
     *      array('source' => <Absolute Source Path>, 'destination' => <Relative Destination Path>),
     *      ......
     * )
     *
     * @return array
     */
    public function getCopyRules()
    {
        $params = array(
            'area'          => '*',
            'theme_path'    => '*',
            'locale'        => null, //temporary locale is not taken into account
            'pool'          => '*',
            'namespace'     => '*',
            'module'        => '*',
        );

        $result = array();
        foreach ($this->_themes as $theme) {
            $params['theme'] = $theme;
            $params['area'] = $theme->getArea();
            $patternDirs = $this->_fallbackList->getPatternDirs($params, false);
            foreach (array_reverse($patternDirs) as $pattern) {
                $srcPaths = glob($pattern['dir']);
                foreach ($srcPaths as $src) {
                    $paramsFromDir = $this->_getParams(
                        str_replace(BP, '', $src),
                        str_replace(
                            array(BP, '<theme_path>'),
                            array('', '<package>/<theme>'),
                            $pattern['pattern']
                        )
                    );
                    if (!empty($paramsFromDir['namespace']) && !empty($paramsFromDir['module'])) {
                        $module = $paramsFromDir['namespace'] . '_' . $paramsFromDir['module'];
                    } else {
                        $module = null;
                    }

                    $result[] = array(
                        'source' => $src,
                        'destination' =>
                            $this->_getDestinationPath('', $theme->getArea(), $theme->getThemePath(), $module)
                    );
                }
            }
        }
        return $result;
    }

    /**
     * Get relative destination path based on parameters. Calls method used in Production Mode in application
     *
     * @param $filename
     * @param $area
     * @param $themePath
     * @param $module
     * @return string
     */
    private function _getDestinationPath($filename, $area, $themePath, $module)
    {
        return Mage_Core_Model_Design_Package::getPublishedViewFileRelPath($area, $themePath, '', $filename, $module);
    }

    /**
     * Get themes from file system
     */
    private function _initThemes()
    {
        $themesDir = BP . '/app/design';
        $dir = dir($themesDir);
        while (false !== ($area = $dir->read())) {
            if ($area == '..' || $area == '.') {
                continue;
            }
            $dirArea = dir($themesDir . DS . $area);
            while (false !== ($package = $dirArea->read())) {
                if ($package == '..' || $package == '.') {
                    continue;
                }
                $dirPackage = dir($themesDir . DS . $area . DS . $package);
                while (false !== ($theme = $dirPackage->read())) {
                    if ($theme == '..' || $theme == '.') {
                        continue;
                    }
                    $themeConfig =
                        simplexml_load_file($themesDir . DS . $area . DS . $package . DS . $theme . DS . 'theme.xml');
                    $themeInfo = $themeConfig->xpath('/design/package/theme');

                    if (isset($themeInfo[0]['parent'])) {
                        $parent = $package . '/' . (string)$themeInfo[0]['parent'];
                    } else {
                        $parent = null;
                    }
                    $this->_themes[$area . '/' . $package . '/' . $theme] =
                        new Generator_ThemeProxy($area, $package . '/' . $theme, $parent);
                }
            }
        }
    }

    /**
     * Extract params from $path using $pattern
     *
     * @param string $path
     * @param string $pattern
     * @return array
     */
    private function _getParams($path, $pattern)
    {
        $path = str_replace(DS, '/', $path);
        $pattern = str_replace(DS, '/', $pattern);
        $params = explode('/', $pattern);
        $pathParts = explode('/', $path);
        $result = array();
        foreach ($params as $k => $param) {
            if (!isset($pathParts[$k])) {
                $result = array();
                break;
            }
            if (!preg_match("/^\<.+\>$/", $param)) {
                continue;
            }
            $param = ltrim($param, '<');
            $param = rtrim($param, '>');
            if (preg_match("/\>_\</", $param)) {
                $pathSubParts = explode('_', $pathParts[$k]);
                $subParams = explode('>_<', $param);
                if (count($subParams) == count($pathSubParts)) {
                    foreach ($subParams as $j => $subParam) {
                        $result[$subParam] = $pathSubParts[$j];
                    }
                } else {
                    $result = array();
                    break;
                }
            } else {
                $result[$param] = $pathParts[$k];
            }
        }
        return $result;
    }
}
