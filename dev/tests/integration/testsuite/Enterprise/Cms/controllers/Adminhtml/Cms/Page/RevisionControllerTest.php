<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Cms
 */
class Enterprise_Cms_Adminhtml_Cms_Page_RevisionControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testPreviewAction()
    {
        $page = new Mage_Cms_Model_Page;
        $page->load('page100', 'identifier'); // fixture cms/page
        $this->getRequest()->setPost('page_id', $page->getId());
        $this->dispatch('admin/cms_page_revision/preview/');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<input id="preview_selected_revision"', $body);
        $this->assertNotContains('<select name="revision_switcher" id="revision_switcher">', $body);
    }

    /**
     * @magentoDataFixture Mage/Admin/_files/user.php
     * @magentoDataFixture Mage/Core/_files/design_change.php
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testDropAction()
    {
        $storeId = Mage::app()->getAnyStoreView(); // fixture design_change
        $this->getRequest()->setParam('preview_selected_store', $storeId);

        $page = new Mage_Cms_Model_Page;
        $page->load('page100', 'identifier'); // fixture cms/page
        $this->getRequest()->setPost('page_id', $page->getId());

        $this->dispatch('admin/cms_page_revision/drop/');
        $this->markTestIncomplete('Response body is empty. TODO: figure out');
        $this->assertContains('skin/frontend/default/modern/default', $this->getResponse()->getBody());
    }
}
