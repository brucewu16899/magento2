<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Email_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Email_Template|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var Zend_Mail|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mail;

    protected function setUp()
    {
        $this->_mail = $this->getMock(
            'Zend_Mail', array('send', 'addTo', 'addBcc', 'setReturnPath', 'setReplyTo'), array('utf-8')
        );
        $this->_model = $this->getMockBuilder('Mage_Core_Model_Email_Template')
            ->setMethods(array('_getMail'))
            ->setConstructorArgs(array(
                $this->getMockBuilder('Mage_Core_Model_Event_Manager')->disableOriginalConstructor()->getMock(),
                $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock(),
                Mage::getObjectManager()->create('Magento_Filesystem')
            ))
            ->getMock();
        $this->_model->expects($this->any())->method('_getMail')->will($this->returnCallback(array($this, 'getMail')));
        $this->_model->setSenderName('sender')->setSenderEmail('sender@example.com')->setTemplateSubject('Subject');
    }

    /**
     * Return a disposable Zend_Mail instance
     *
     * @return PHPUnit_Framework_MockObject_MockObject|Zend_Mail
     */
    public function getMail()
    {
        return clone $this->_mail;
    }

    public function testSetGetTemplateFilter()
    {
        $filter = $this->_model->getTemplateFilter();
        $this->assertSame($filter, $this->_model->getTemplateFilter());
        $this->assertEquals(Mage::app()->getStore()->getId(), $filter->getStoreId());

        $filter = Mage::getModel('Mage_Core_Model_Email_Template_Filter');
        $this->_model->setTemplateFilter($filter);
        $this->assertSame($filter, $this->_model->getTemplateFilter());
    }

    public function testLoadDefault()
    {
        $this->_model->loadDefault('customer_create_account_email_template');
        $this->assertNotEmpty($this->_model->getTemplateText());
        $this->assertNotEmpty($this->_model->getTemplateSubject());
        $this->assertNotEmpty($this->_model->getOrigTemplateVariables());
        $this->assertInternalType('array', Zend_Json::decode($this->_model->getOrigTemplateVariables()));
        $this->assertNotEmpty($this->_model->getTemplateStyles());
    }

    public function testDefaultTemplateAsOptionsArray()
    {
        $options = $this->_model->getDefaultTemplatesAsOptionsArray();
        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('group', $option);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testGetProcessedTemplate()
    {
        $this->_setBlueThemeForFixtureStore();
        $expectedViewUrl = 'theme/static/frontend/default/demo_blue/en_US/Mage_Page/favicon.ico';
        $this->_model->setTemplateText('{{view url="Mage_Page::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplate());
        $this->_model->setDesignConfig(array(
            'area' => 'frontend', 'store' => Mage::app()->getStore('fixturestore')->getId()
        ));
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplate());
    }

    /**
     * Set 'default/demo_blue' for the 'fixturestore' store.
     * Application isolation is required, if a test uses this method.
     */
    protected function _setBlueThemeForFixtureStore()
    {
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        $theme->load('default/demo_blue', 'theme_path');
        Mage::app()->getStore('fixturestore')
            ->setConfig(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID, $theme->getId());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/design_change.php
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testGetProcessedTemplateDesignChange()
    {
        $this->_model->setTemplateText('{{view url="Mage_Page::favicon.ico"}}');
        $this->assertStringEndsWith(
            'theme/static/frontend/default/modern/en_US/Mage_Page/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Core/_files/store.php
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testGetProcessedTemplateSubject()
    {
        $this->_setBlueThemeForFixtureStore();
        $expectedViewUrl = 'theme/static/frontend/default/demo_blue/en_US/Mage_Page/favicon.ico';
        $this->_model->setTemplateSubject('{{view url="Mage_Page::favicon.ico"}}');
        $this->assertStringEndsNotWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
        $this->_model->setDesignConfig(array(
            'area' => 'frontend', 'store' => Mage::app()->getStore('fixturestore')->getId()
        ));
        $this->assertStringEndsWith($expectedViewUrl, $this->_model->getProcessedTemplateSubject(array()));
    }

    /**
     * @covers Mage_Core_Model_Email_Template::send
     * @covers Mage_Core_Model_Email_Template::addBcc
     * @covers Mage_Core_Model_Email_Template::setReturnPath
     * @covers Mage_Core_Model_Email_Template::setReplyTo
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testSend()
    {
        $this->_mail->expects($this->exactly(2))->method('send');
        $this->_mail->expects($this->once())->method('addBcc')->with('bcc@example.com');
        $this->_mail->expects($this->once())->method('setReturnPath')->with('return@example.com');
        $this->_mail->expects($this->once())->method('setReplyTo')->with('replyto@example.com');

        $this->_model->addBcc('bcc@example.com')
            ->setReturnPath('return@example.com')
            ->setReplyTo('replyto@example.com')
        ;
        $this->assertNull($this->_model->getSendingException());
        $this->assertTrue($this->_model->send('test@example.com'));
        $this->assertNull($this->_model->getSendingException());

        // send once again to make sure bcc, return path and reply-to were not invoked second time
        $this->assertTrue($this->_model->send('test@example.com'));
    }

    /**
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testSendMultipleRecipients()
    {
        $this->_mail->expects($this->at(0))->method('addTo')->with('one@example.com', '=?utf-8?B?TmFtZSBPbmU=?=');
        $this->_mail->expects($this->at(1))->method('addTo')->with('two@example.com', '=?utf-8?B?dHdv?=');
        $this->assertTrue($this->_model->send(array('one@example.com', 'two@example.com'), array('Name One')));
    }

    public function testSendFailure()
    {
        $exception = new Exception('test');
        $this->_mail->expects($this->once())->method('send')->will($this->throwException($exception));

        $this->assertNull($this->_model->getSendingException());
        $this->assertFalse($this->_model->send('test@example.com'));
        $this->assertSame($exception, $this->_model->getSendingException());
    }

    /**
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     */
    public function testSendTransactional()
    {
        $this->_model->sendTransactional('customer_create_account_email_template',
            array('name' => 'Sender Name', 'email' => 'sender@example.com'), 'recipient@example.com', 'Recipient Name'
        );
        $this->assertEquals('customer_create_account_email_template', $this->_model->getId());
        $this->assertTrue($this->_model->getSentSuccess());
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testSendTransactionalWrongId()
    {
        $this->_model->sendTransactional('wrong_id' . uniqid(),
            array('name' => 'Sender Name', 'email' => 'sender@example.com'), 'recipient@example.com', 'Recipient Name'
        );
    }
}
