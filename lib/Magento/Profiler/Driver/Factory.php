<?php
/**
 * Profiler driver factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Factory
{
    /**
     * Default driver type
     *
     * @var string
     */
    protected $_defaultDriverType;

    /**
     * Default driver class prefix
     *
     * @var string
     */
    protected $_defaultDriverClassPrefix;

    /**
     * Constructor
     *
     * @param string $defaultDriverClassPrefix
     * @param string $defaultDriverType
     */
    public function __construct($defaultDriverClassPrefix = 'Magento_Profiler_Driver_', $defaultDriverType = 'standard')
    {
        $this->_defaultDriverClassPrefix = $defaultDriverClassPrefix;
        $this->_defaultDriverType = $defaultDriverType;
    }

    /**
     * Create instance of profiler driver
     *
     * @param Magento_Profiler_Driver_Configuration $configuration
     * @throws InvalidArgumentException
     * @return Magento_Profiler_DriverInterface
     */
    public function create(Magento_Profiler_Driver_Configuration $configuration)
    {
        $type = $configuration->getTypeValue($this->_defaultDriverType);
        if (class_exists($type)) {
            $class = $type;
        } else {
            $class = $this->_defaultDriverClassPrefix . ucfirst($type);
            if (!class_exists($class)) {
                throw new InvalidArgumentException(
                    sprintf("Cannot create profiler driver, class \"%s\" doesn't exist.", $class
                ));
            }
        }
        $driver = new $class($configuration);
        if (!$driver instanceof Magento_Profiler_DriverInterface) {
            throw new InvalidArgumentException(sprintf(
                "Driver class \"%s\" must implement Magento_Profiler_DriverInterface.", get_class($driver)
            ));
        }
        return $driver;
    }
}
