<?php

/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unit testing helper for Magento models.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Model_Model extends Mage_PHPUnit_Helper_Model_Abstract
{
    /**
     * Name of the pool with model's real class names
     *
     * @var string
     */
    protected $_realModelClassesPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_REAL_MODEL_CLASSES;

    /**
     * Group type name
     *
     * @var string
     */
    protected $_group = 'model';

    /**
     * Returns pool of real model class names
     *
     * @return Mage_PHPUnit_StaticDataPool_ModelClass
     */
    protected function _getModelClassNamesPool()
    {
        return $this->_getStaticDataObject($this->_realModelClassesPool);
    }

    /**
     * Returns real model class name
     *
     * @param string $modelName
     * @return string
     */
    public function getRealModelClass($modelName)
    {
        $className = $this->_getModelClassNamesPool()->getRealModelClass($modelName);
        if (!$className) {
            $className = $this->_getModelClassNameFromConfig($modelName);
            $this->_getModelClassNamesPool()->setRealModelClass($modelName, $className);
        }
        return $className;
    }

    /**
     * Gets model's real class name from config object,
     * but does not add it to config object's cache array.
     * So, this method is safe for models rewriting.
     *
     * @param string $model
     * @return string
     */
    protected function _getModelClassNameFromConfig($model)
    {
        $classArr = explode('/', trim($model));
        $module = $classArr[0];
        $class = !empty($classArr[1]) ? $classArr[1] : null;

        $config = Mage::getConfig()->getNode("global/{$this->_group}s/{$module}");

        if (is_object($config) && $config->rewrite->{$class}) {
            $className = (string)$config->rewrite->{$class};
        } else {
            if (!empty($config)) {
                if ($config->class) {
                    $modelNew = (string)$config->class;
                } elseif ($config->model) {
                    $modelNew = (string)$config->model;
                } else {
                    $className = false;
                    $modelNew = false;
                }
                if ($modelNew) {
                    $modelNew = trim($modelNew);
                    if (strpos($modelNew, '/')===false) {
                        $className = $modelNew;
                    } else {
                        $className = $this->_getModelClassName($modelNew);
                    }
                }
            }
            if (empty($className)) {
                $className = 'mage_'.$module.'_'.$this->_group;
            }
            if (!empty($class)) {
                $className .= '_'.$class;
            }
            $className = uc_words($className);
        }

        return $className;
    }

    /**
     * Rewrite model by delegator class.
     * You can rewrite one model only once for one test.
     *
     * @param string $model
     * @param string $className delegator class name
     */
    public function rewriteModelByClass($model, $className)
    {
        list($module, $modelName) = explode('/', $model);
        $nodePath = "global/{$this->_group}s/{$module}/rewrite/{$modelName}";
        if (Mage::getConfig()->getNode($nodePath) != $className) {
            Mage::getConfig()->setNode($nodePath, $className);
        }
    }
}