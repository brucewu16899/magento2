<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */
class Legacy_ClassesTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of already found classes to avoid checking them over and over again
     *
     * @var array
     */
    protected static $_existingClasses = array();

    /**
     * Collect class names by patterns
     *
     * @param string $file
     * @dataProvider FileDataProvider::getPhpFiles
     */
    public function testPhpCode($file)
    {
        $classes = self::collectPhpCodeClasses(file_get_contents($file));
        $this->_assertClassesNamedCorrect($classes, $file);
    }

    /**
     * Scan contents as PHP-code and find class name occurrences
     *
     * @param string $contents
     * @param array &$classes
     * @return array
     */
    public static function collectPhpCodeClasses($contents, &$classes = array())
    {
        self::collectMatches($contents, '/
            # ::getModel ::getSingleton ::getResourceModel ::getResourceSingleton
            \:\:get(?:Resource)?(?:Model | Singleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # addBlock createBlock getBlockClassName getBlockSingleton
            | (?:addBlock | createBlock | getBlockClassName | getBlockSingleton)\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # Mage::helper ->helper
            | (?:Mage\:\:|\->)helper\(\s*[\'"]([^\'"]+)[\'"]\s*\)

            # various methods, first argument
            | \->(?:initReport | setDataHelperName | setEntityModelClass | _?initLayoutMessages
                | setAttributeModel | setBackendModel | setFrontendModel | setSourceModel | setModel
            )\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # various methods, second argument
            | \->add(?:ProductConfigurationHelper | OptionsRenderCfg)\(.+,\s*[\'"]([^\'"]+)[\'"]\s*[\),]

            # models in install or setup
            | [\'"](?:resource_model | attribute_model | entity_model | entity_attribute_collection
                | source | backend | frontend | input_renderer | frontend_input_renderer
            )[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]

            # misc
            | function\s_getCollectionClass\(\)\s+{\s+return\s+[\'"]([a-z\d_\/]+)[\'"]
            | _parentResourceModelName\s*=\s*\'([a-z\d_\/]+)\'

            /Uix', $classes
        );

        // check ->_init | parent::_init
        $skipForInit = implode('|',
            array('id', '[\w\d_]+_id', 'pk', 'code', 'status', 'serial_number', 'entity_pk_value', 'currency_code')
        );
        self::collectMatches($contents, '/
            (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*\)
            | (?:parent\:\: | \->)_init\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]((?!(' . $skipForInit . '))[^\'"]+)[\'"]\s*\)
            /Uix', $classes
        );

        return $classes;
    }

    /**
     * Return list of php and phtml files
     *
     * @return array
     */
    public function phpFileDataProvider()
    {
        return FileDataProvider::getPhpFiles();
    }

    /**
     * Check by parentNodeName if currentNode under parent node
     *
     * @param $currentNode SimpleXMLElement
     * @param $parentNodeName
     * @return bool
     */
    protected function _isUnderParentNode($currentNode, $parentNodeName)
    {
        /** @var $parentNode SimpleXMLElement */
        $parentNode = current($currentNode->xpath("parent::*"));
        if (!$parentNode) {
            return false;
        }

        if ($parentNode->getName() != $parentNodeName) {
            return $this->_isUnderParentNode($parentNode, $parentNodeName);
        }

        return true;
    }

    /**
     * Collect class names by xpath in configurable files
     *
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfiguration($path)
    {
        $xml = simplexml_load_file($path);
        $classes = array();

        // various nodes
        // excluding class in /config/sections
        $nodes = $xml->xpath('/config//resource_adapter | /config/*[not(name()="sections")]//class
            | //backend_model | //source_model | //price_model | //writer_model | //clone_model | //frontend_model
            | //model_token | //admin_renderer | //renderer'
        ) ?: array();
        foreach ($nodes as $node) {
            if (preg_match('/([\w\d_\/]+)\:{0,2}/i', (string)$node, $matches)) {
                $classes[$matches[1]] = 1;
            }
        }

        // "backend_model" attribute
        $nodes = $xml->xpath('//@backend_model') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            $classes[$node['@attributes']['backend_model']] = 1;
        }

        $this->_collectModels($xml, $classes);
        $this->_collectLoggingExpectedModels($xml, $classes);

        $this->_assertClassesNamedCorrect(array_keys($classes), $path);
    }

    /**
     * Return list of configurable files
     *
     * @return array
     */
    public function configFileDataProvider()
    {
        return FileDataProvider::getConfigFiles();
    }

    /**
     * Special case: collect models not inside staging_items node
     *
     * @param SimpleXmlElement $xml
     * @param array &$classes
     */
    protected function _collectModels($xml, &$classes)
    {
        $nodes = $xml->xpath('//model') ?: array();
        foreach ($nodes as $node) {
            // excluding model in //staging_items/*
            if ($this->_isUnderParentNode($node, 'staging_items') && $node->getName() == 'model') {
                continue;
            }

            if (preg_match('/([\w\d_\/]+)\:{0,2}/i', (string)$node, $matches)) {
                $classes[$matches[1]] = 1;
            }
        }
    }


    /**
     * Special case: collect "expected models" from logging xml-file
     *
     * @param SimpleXmlElement $xml
     * @param array &$classes
     */
    protected function _collectLoggingExpectedModels($xml, &$classes)
    {
        $nodes = $xml->xpath('/logging/*/expected_models/* | /logging/*/actions/*/expected_models/*') ?: array();
        foreach ($nodes as $node) {
            $classes[$node->getName()] = 1;
        }
    }

    /**
     * Collect class names by xpath in configurable files
     *
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfigAttributeModule($path)
    {
        $xml = simplexml_load_file($path);
        $modules = $classes = array();

        // various nodes
        $nodes = $xml->xpath('//*[@module]') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            if (isset($node['@attributes']['module'])) {
                $modules[$node['@attributes']['module']] = 1;
                $class = $node['@attributes']['module'] . '_Helper_Data';
                $classes[$class] = 1;
            }
        }

        $this->_assertModulesNamedCorrect(array_keys($modules), $path);
        $this->_assertClassesNamedCorrect(array_keys($classes), $path);
    }


    /**
     * Collect class names from layout files
     *
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayouts($path)
    {
        $xml = simplexml_load_file($path);
        $classes = array();

        // block@type
        $nodes = $xml->xpath('/layout//block[@type]') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            $class = $node['@attributes']['type'];
            $classes[(string)$class] = 1;
        }

        // any text nodes that contain conventional block/model/helper names
        $nodes = $xml->xpath('/layout//action/attributeType | /layout//renderer_block | /layout//renderer
            | /layout//action[@method="addTab"]/content
            | /layout//action[@method="addRenderer" or @method="addItemRender" or @method="addColumnRender"
                or @method="addPriceBlockType" or @method="addMergeSettingsBlockType"
                or @method="addInformationRenderer" or @method="addOptionRenderer" or @method="addRowItemRender"
                or @method="addDatabaseBlock"]/block
            | /layout//action[@method="setMassactionBlockName" or @method="addProductConfigurationHelper"]/name
            | /layout//action[@method="setEntityModelClass"]/code'
        ) ?: array();
        foreach ($nodes as $node) {
            $classes[(string)$node] = 1;
        }

        $this->_collectLayoutHelpersAndModules($xml, $classes);

        $this->_assertClassesNamedCorrect(array_keys($classes), $path);
    }

    /**
     * Return list of layout files
     *
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return FileDataProvider::getLayoutFiles();
    }

    /**
     * Special case: collect declaration of helpers and modules in layout files and figure out helper class names
     *
     * @param SimpleXmlElement $xml
     * @param array &$classes
     */
    protected function _collectLayoutHelpersAndModules($xml, &$classes)
    {
        $nodes = $xml->xpath('/layout//@helper | /layout//@module') ?: array();
        foreach ($nodes as $node) {
            $node = (array)$node;
            if (isset($node['@attributes']['helper'])) {
                $class = explode('::', $node['@attributes']['helper']);
                $classes[array_shift($class)] = 1;
            }
            if (isset($node['@attributes']['module'])) {
                $class = $node['@attributes']['module'] . '_Helper_Data';
                $classes[$class] = 1;
            }
        }
    }

    /**
     * Sub-routine to find all unique matches in specified content using specified PCRE
     *
     * @param string $contents
     * @param string $regex
     * @param array &$result
     * @return array
     */
    public static function collectMatches($contents, $regex, &$result = array())
    {
        preg_match_all($regex, $contents, $matches);

        array_shift($matches);
        foreach ($matches as $row) {
            $result = array_merge($result, $row);
        }
        $result = array_filter(array_unique($result), function($value) {
            return !empty($value);
        });
        return $result;
    }

    /**
     * Check whether specified classes correspond to a file according PSR-0 standard
     *
     * Cyclomatic complexity is because of temporary marking test as incomplete
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $classes
     * @param string $fileName
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertClassesNamedCorrect($classes, $fileName)
    {
        if (!$classes) {
            return;
        }
        $badClasses = array();
        foreach ($classes as $class) {
            try {
                $matches = array();
                $this->assertTrue(preg_match('/(?:[^\w\d_]|__)/i', $class, $matches) == 0);
                array_filter(
                array_map(function ($value) {
                    return ucfirst($value) === $value;
                },
                explode('_', $class)
                ),
                array($this, "assertTrue")
                );
                self::$_existingClasses[$class] = 1;
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                if ('Mage_Catalog_Model_Resource_Convert' == $class) {
                    $this->markTestIncomplete('Bug MAGE-4763');
                }

                $badClasses[] = $class;
            }
        }
        if ($badClasses) {
            $this->fail("Incorrect class(es) declaration in {$fileName}:\n" . implode("\n", $badClasses));
        }
    }

    /**
     * Check whether specified classes correspond to a file according PSR-0 standard
     *
     * Cyclomatic complexity is because of temporary marking test as incomplete
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $modules
     * @param string $fileName
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertModulesNamedCorrect($modules, $fileName)
    {
        if (!$modules) {
            return;
        }

        $codeLocation = PATH_TO_SOURCE_CODE . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code';
        $locations = array(
            $codeLocation . DIRECTORY_SEPARATOR . 'community',
            $codeLocation . DIRECTORY_SEPARATOR . 'core',
            $codeLocation . DIRECTORY_SEPARATOR . 'local'
        );

        $badModules = array();
        foreach ($modules as $module) {
            $modulesLocation = array();
            foreach ($locations as $location) {
                $modulesLocation[] = $location . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $module);
            }
            try {
                $this->assertContains(true, array_map('is_dir', $modulesLocation));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $badModules[] = $module;
            }
        }

        if ($badModules) {
            $this->fail("Incorrect module(es) declaration in {$fileName}:\n" . implode("\n", $badModules));
        }
    }
}
