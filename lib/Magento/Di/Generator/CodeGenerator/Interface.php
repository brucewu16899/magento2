<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Di_Generator_CodeGenerator_Interface extends \Zend\Code\Generator\GeneratorInterface
{
    /**
     * @param string $name
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setName($name);

    /**
     * @param array $docBlock
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setClassDocBlock(array $docBlock);

    /**
     * @param array $properties
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function addProperties(array $properties);

    /**
     * @param array $methods
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function addMethods(array $methods);

    /**
     * @param string $extendedClass
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setExtendedClass($extendedClass);

    /**
     * setImplementedInterfaces()
     *
     * @param array $interfaces
     * @return Magento_Di_Generator_CodeGenerator_Interface
     */
    public function setImplementedInterfaces(array $interfaces);
}
