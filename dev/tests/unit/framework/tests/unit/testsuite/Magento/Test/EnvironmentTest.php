<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_EnvironmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir;

    /**
     * @var Magento_Test_Environment
     */
    protected $_environment;

    /**
     * Calculate directories
     */
    public static function setUpBeforeClass()
    {
        self::$_tmpDir = realpath(dirname(__FILE__) . '/../../../../../../tmp');
    }

    protected function setUp()
    {
        $this->_environment = new Magento_Test_Environment(self::$_tmpDir);
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testGetInstance()
    {
        Magento_Test_Environment::getInstance();
    }

    /**
     * @depends testGetInstance
     */
    public function testSetGetInstance()
    {
        Magento_Test_Environment::setInstance($this->_environment);
        $this->assertSame($this->_environment, Magento_Test_Environment::getInstance());
    }

    public function testGetTmpDir()
    {
        $this->assertEquals(self::$_tmpDir, $this->_environment->getTmpDir());
    }

    public function testCleanTmpDir()
    {
        $fileName = self::$_tmpDir . '/file.tmp';
        touch($fileName);

        try {
            $this->_environment->cleanTmpDir();
            $this->assertFileNotExists($fileName);
        } catch (Exception $e) {
            unlink($fileName);
            throw $e;
        }
    }

    public function testCleanDir()
    {
        $dir = self::$_tmpDir . '/subtmp';
        mkdir($dir, 0777);
        $fileName = $dir . '/file.tmp';
        touch($fileName);

        try {
            $this->_environment->cleanDir(self::$_tmpDir);
            $this->assertFalse(is_dir($dir));
        } catch (Exception $e) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            rmdir($dir);
            throw $e;
        }
    }
}
