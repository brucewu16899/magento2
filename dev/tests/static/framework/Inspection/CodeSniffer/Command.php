<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PHP Code Sniffer shell command
 */
class Inspection_CodeSniffer_Command extends Inspection_CommandAbstract
{
    /**
     * @var string
     */
    protected $_rulesetDir;

    /**
     * Constructor
     *
     * @param string $rulesetDir Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     * @param array $whiteList Files/folders to be inspected
     * @param array $blackList Files/folders to be excluded from the inspection
     */
    public function __construct($rulesetDir, $reportFile, array $whiteList, array $blackList = array())
    {
        parent::__construct($reportFile, $whiteList, $blackList);
        $this->_rulesetDir = $rulesetDir;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpcs --version';
    }

    /**
     * @return string
     */
    protected function _buildShellCmd()
    {
        $whiteList = $this->_whiteList;
        $whiteList = array_map('escapeshellarg', $whiteList);
        $whiteList = implode(' ', $whiteList);

        /* Note: phpcs allows regular expressions for the ignore list */
        $blackList = '';
        if ($this->_blackList) {
            foreach ($this->_blackList as $fileOrDir) {
                $fileOrDir = str_replace('/', DIRECTORY_SEPARATOR, $fileOrDir);
                $blackList .= ($blackList ? ',' : '') . preg_quote($fileOrDir);
            }
            $blackList = '--ignore=' . escapeshellarg($blackList);
        }

        return 'phpcs'
            . ($blackList ? ' ' . $blackList : '')
            . ' --standard=' . escapeshellarg($this->_rulesetDir)
            . ' --report=checkstyle'
            . ' --report-file=' . escapeshellarg($this->_reportFile)
            . ' -n'
            . ' ' . $whiteList
        ;
    }
}