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
 * PHP Copy/Paste Detector shell command
 */
class Inspection_CopyPasteDetector_Command extends Inspection_CommandAbstract
{
    /**
     * @var int|null
     */
    protected $_minLines;

    /**
     * @var int|null
     */
    protected $_minTokens;

    /**
     * Constructor
     *
     * @param string $reportFile Destination file to write inspection report to
     * @param int|null $minLines Minimum number of identical lines
     * @param int|null $minTokens Minimum number of identical tokens
     */
    public function __construct($reportFile, $minLines = null, $minTokens = null)
    {
        parent::__construct($reportFile);
        $this->_minLines = $minLines;
        $this->_minTokens = $minTokens;
    }

    /**
     * @return string
     */
    public function _buildVersionShellCmd()
    {
        return 'phpcpd --version';
    }

    /**
     * @param array $whiteList
     * @param array $blackList
     * @return string
     */
    protected function _buildShellCmd($whiteList, $blackList)
    {
        $whiteList = array_map('escapeshellarg', $whiteList);
        $whiteList = implode(' ', $whiteList);

        if ($blackList) {
            $blackList = array_map('escapeshellarg', $blackList);
            $blackList = '--exclude ' . implode(' --exclude ', $blackList);
        } else {
            $blackList = '';
        }

        return 'phpcpd'
            . ' --log-pmd ' . escapeshellarg($this->_reportFile)
            . ($blackList ? ' ' . $blackList : '')
            . ($this->_minLines ? ' --min-lines ' . $this->_minLines : '')
            . ($this->_minTokens ? ' --min-tokens ' . $this->_minTokens : '')
            . ' ' . $whiteList
        ;
    }

    /**
     * Runs command and produces report in html format
     *
     * @param array $whiteList Files/directories to be inspected
     * @param array $blackList Files/directories to be excluded from the inspection
     * @return bool
     */
    public function run(array $whiteList, array $blackList = array())
    {
        $result = parent::run($whiteList, $blackList);
        if ($result) {
            $generateHtmlResult = $this->_generateHtmlReport();
            if ($generateHtmlResult === false) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Generate HTML representation for an existing XML report using XSLT transformation
     *
     * @return bool|null
     */
    protected function _generateHtmlReport()
    {
        if ($this->_execShellCmd('xsltproc --version') === false) {
            return null;
        }
        $xsltFile = __DIR__ . '/html_report.xslt';
        $result = $this->_execShellCmd(sprintf(
            "xsltproc %s %s > %s",
            escapeshellarg($xsltFile),
            escapeshellarg($this->_reportFile),
            escapeshellarg("{$this->_reportFile}.html")
        ));
        $this->_generateLastRunMessage();
        return ($result !== false);
    }
}
