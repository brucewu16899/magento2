<?php
/**
 * Standard profiler driver output factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_Factory
{
    /**
     * Default output type
     *
     * @var string
     */
    protected $_defaultOutputType;

    /**
     * Default output class prefix
     *
     * @var string
     */
    protected $_defaultOutputClassPrefix;

    /**
     * Constructor
     *
     * @param string $defaultOutputClassPrefix
     * @param string $defaultOutputType
     */
    public function __construct(
        $defaultOutputClassPrefix = 'Magento_Profiler_Driver_Standard_Output_',
        $defaultOutputType = 'html'
    ) {
        $this->_defaultOutputClassPrefix = $defaultOutputClassPrefix;
        $this->_defaultOutputType = $defaultOutputType;
    }

    /**
     * Create instance of standard profiler driver output
     *
     * @param Magento_Profiler_Driver_Standard_Output_Configuration $config
     * @return Magento_Profiler_Driver_Standard_OutputInterface
     * @throws InvalidArgumentException If driver cannot be created
     */
    public function create(Magento_Profiler_Driver_Standard_Output_Configuration $config)
    {
        $type = $config->getTypeValue($this->_defaultOutputType);
        if (class_exists($type)) {
            $class = $type;
        } else {
            $class = $this->_defaultOutputClassPrefix . ucfirst($type);
            if (!class_exists($class)) {
                throw new InvalidArgumentException(
                    sprintf("Cannot create standard driver output, class \"%s\" doesn't exist.", $class
                ));
            }
        }
        $output = new $class($config);
        if (!$output instanceof Magento_Profiler_Driver_Standard_OutputInterface) {
            throw new InvalidArgumentException(sprintf(
                "Output class \"%s\" must implement Magento_Profiler_Driver_Standard_OutputInterface.",
                get_class($output)
            ));
        }
        return $output;
    }
}
