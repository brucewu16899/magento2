<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class with substitution parameters to values considering theme hierarchy
 */
class Mage_Core_Model_Design_Fallback_Rule_Theme implements Mage_Core_Model_Design_Fallback_Rule_RuleInterface
{
    /**
     * Constructor
     *
     * @param array $patternsArray
     * @throws InvalidArgumentException
     */
    public function __construct(array $patternsArray)
    {
        foreach ($patternsArray as $pattern) {
            if (!is_array($pattern)) {
                throw new InvalidArgumentException("Each pattern in list must be an array");
            }
            if (strpos($pattern[0], '<theme_path>') === false) {
                throw new InvalidArgumentException("Pattern must contain '<theme_path>' node");
            }
        }
        $this->_patternsArray = $patternsArray;
    }

    /**
     * Get ordered list of folders to search for a file
     *
     * @param array $params - array of parameters
     * @return array of folders to perform a search
     * @throws InvalidArgumentException
     */
    public function getPatternDirs($params)
    {
        $patterns = array();
        if (!array_key_exists('theme', $params) || !($params['theme'] instanceof Mage_Core_Model_ThemeInterface)) {
            throw new InvalidArgumentException(
                '$params["theme"] should be passed and should implement Mage_Core_Model_ThemeInterface'
            );
        }

        foreach ($this->_getThemeList($params['theme']) as $theme) {
            $params['theme_path'] = $theme->getThemePath();
            if ($params['theme_path']) {
                foreach ($this->_patternsArray as $pattern) {
                    $simplePattern = $pattern[0];
                    $optionalParams = empty($pattern[1]) ? array() : $pattern[1];
                    $simpleRule = new Mage_Core_Model_Design_Fallback_Rule_Simple($simplePattern, $optionalParams);
                    $patterns = array_merge($patterns, $simpleRule->getPatternDirs($params));
                }
            }
        }
        return $patterns;
    }

    /**
     * Get list of themes, which should be used for fallback. It's passed theme and all its parent themes
     *
     * @param Mage_Core_Model_ThemeInterface $theme
     * @return array
     */
    protected function _getThemeList(Mage_Core_Model_ThemeInterface $theme)
    {
        $result = array();
        $themeModel = $theme;
        while ($themeModel) {
            $result[] = $themeModel;
            $themeModel = $themeModel->getParentTheme();
        }
        return $result;
    }
}
