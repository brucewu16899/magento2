<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    $this->_getClassRule('Mage_Admin_Helper_Data', 'Mage_Backend_Helper_Data'),
    $this->_getClassRule('Mage_Admin_Model_Acl', 'Magento_Acl'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Role'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Resource', 'Magento_Acl_Resource'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Role_Registry', 'Magento_Acl_Role_Registry'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Role_Generic', 'Mage_User_Model_Acl_Role_Generic'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Role_Group', 'Mage_User_Model_Acl_Role_Group'),
    $this->_getClassRule('Mage_Admin_Model_Acl_Role_User', 'Mage_User_Model_Acl_Role_User'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Acl', 'Mage_User_Model_Resource_Acl'),
    $this->_getClassRule('Mage_Admin_Model_Observer'),
    $this->_getClassRule('Mage_Admin_Model_Session', 'Mage_Backend_Model_Auth_Session'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Acl_Role'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Acl_Role_Collection'),
    $this->_getClassRule('Mage_Admin_Model_User', 'Mage_User_Model_User'),
    $this->_getClassRule('Mage_Admin_Model_Config'),
    $this->_getClassRule('Mage_Admin_Model_Resource_User', 'Mage_User_Model_Resource_User'),
    $this->_getClassRule('Mage_Admin_Model_Resource_User_Collection', 'Mage_User_Model_Resource_User_Collection'),
    $this->_getClassRule('Mage_Admin_Model_Role', 'Mage_User_Model_Role'),
    $this->_getClassRule('Mage_Admin_Model_Roles', 'Mage_User_Model_Roles'),
    $this->_getClassRule('Mage_Admin_Model_Rules', 'Mage_User_Model_Rules'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Role', 'Mage_User_Model_Resource_Role'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Roles', 'Mage_User_Model_Resource_Roles'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Rules', 'Mage_User_Model_Resource_Rules'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Role_Collection', 'Mage_User_Model_Resource_Role_Collection'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Roles_Collection', 'Mage_User_Model_Resource_Roles_Collection'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Roles_User_Collection',
        'Mage_User_Model_Resource_Roles_User_Collection'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Rules_Collection', 'Mage_User_Model_Resource_Rules_Collection'),
    $this->_getClassRule('Mage_Admin_Model_Resource_Permissions_Collection',
        'Mage_User_Model_Resource_Permissions_Collection'),
    $this->_getClassRule('Mage_Adminhtml_Block_Api_Edituser'),
    $this->_getClassRule('Mage_Adminhtml_Block_Api_Tab_Userroles'),
    $this->_getClassRule('Mage_Adminhtml_Block_Catalog'),
    $this->_getClassRule('Mage_Adminhtml_Block_Page_Menu', 'Mage_Backend_Block_Menu'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Grid'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Edit'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Edit_Tabs'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Roles'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_User_Edit_Form'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Role'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Buttons'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Role_Grid_User'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Grid_Role'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Grid_User'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Tab_Roleinfo'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Tab_Rolesedit'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Tab_Rolesusers'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Tab_Useredit'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Editroles'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Roles'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Users'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Edituser'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Tab_Userroles'),
    $this->_getClassRule('Mage_Adminhtml_Block_Permissions_Usernroles'),
    $this->_getClassRule('Mage_Adminhtml_Permissions_UserController'),
    $this->_getClassRule('Mage_Adminhtml_Permissions_RoleController'),
    $this->_getClassRule('Mage_Adminhtml_Block_Report_Product_Ordered'),
    $this->_getClassRule('Mage_Adminhtml_Block_Report_Product_Ordered_Grid'),
    $this->_getClassRule('Mage_Adminhtml_Block_Sales'),
    $this->_getClassRule('Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Giftmessage'),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Edit', 'Mage_Backend_Block_System_Config_Edit'),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form', 'Mage_Backend_Block_System_Config_Form'),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Tabs', 'Mage_Backend_Block_System_Config_Tabs'),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_System_Storage_Media_Synchronize',
        'Mage_Backend_Block_System_Config_System_Storage_Media_Synchronize'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput',
        'Mage_Backend_Block_System_Config_Form_Fieldset_Modules_DisableOutput'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Regexceptions',
        'Mage_Backend_Block_System_Config_Form_Field_Regexceptions'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Notification',
        'Mage_Backend_Block_System_Config_Form_Field_Notification'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Heading',
        'Mage_Backend_Block_System_Config_Form_Field_Heading'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Datetime',
        'Mage_Backend_Block_System_Config_Form_Field_Datetime'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract',
        'Mage_Backend_Block_System_Config_Form_Field_Array_Abstract'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Fieldset',
        'Mage_Backend_Block_System_Config_Form_Fieldset'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field',
        'Mage_Backend_Block_System_Config_Form_Field'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Import',
        'Mage_Backend_Block_System_Config_Form_Field_Import'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Image',
        'Mage_Backend_Block_System_Config_Form_Field_Image'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Export',
        'Mage_Backend_Block_System_Config_Form_Field_Export'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Allowspecific',
        'Mage_Backend_Block_System_Config_Form_Field_Select_Allowspecific'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_File',
        'Mage_Backend_Block_System_Config_Form_Field_File'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatproduct',
        'Mage_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatproduct'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Field_Select_Flatcatalog',
        'Mage_Catalog_Block_Adminhtml_System_Config_Form_Field_Select_Flatcatalog'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Form_Fieldset_Order_Statuses',
        'Mage_Sales_Block_Adminhtml_System_Config_Form_Fieldset_Order_Statuses'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Dwstree', 'Mage_Backend_Block_System_Config_Dwstree'),
    $this->_getClassRule('Mage_Adminhtml_Block_System_Config_Switcher', 'Mage_Backend_Block_System_Config_Switcher'),
    $this->_getClassRule('Mage_Adminhtml_Block_Store_Switcher', 'Mage_Backend_Block_Store_Switcher'),
    $this->_getClassRule('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset',
        'Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element',
        'Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element'
    ),
    $this->_getClassRule('Mage_Adminhtml_Block_Tag_Tag_Edit'),
    $this->_getClassRule('Mage_Adminhtml_Block_Tag_Tag_Edit_Form'),
    $this->_getClassRule('Mage_Adminhtml_Block_Tree'),
    $this->_getClassRule('Mage_Adminhtml_Helper_Rss'),
    $this->_getClassRule('Mage_Adminhtml_Model_Config', 'Mage_Backend_Model_Config_Structure'),
    $this->_getClassRule('Mage_Adminhtml_Model_Config_Data', 'Mage_Backend_Model_Config'),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allowedmethods'),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Password_Link_Expirationperiod',
        'Mage_Backend_Model_Config_Backend_Admin_Password_Link_Expirationperiod'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom',
        'Mage_Backend_Model_Config_Backend_Admin_Custom'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Custompath',
        'Mage_Backend_Model_Config_Backend_Admin_Custompath'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Observer',
        'Mage_Backend_Model_Config_Backend_Admin_Observer'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Robots',
        'Mage_Backend_Model_Config_Backend_Admin_Robots'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustom',
        'Mage_Backend_Model_Config_Backend_Admin_Usecustom'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustompath',
        'Mage_Backend_Model_Config_Backend_Admin_Usecustompath'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Admin_Usesecretkey',
        'Mage_Backend_Model_Config_Backend_Admin_Usesecretkey'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Catalog_Inventory_Managestock',
        'Mage_CatalogInventory_Model_Config_Backend_Managestock'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Catalog_Search_Type',
        'Mage_CatalogSearch_Model_Config_Backend_Search_Type'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract',
        'Mage_Backend_Model_Config_Backend_Currency_Abstract'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Currency_Allow',
        'Mage_Backend_Model_Config_Backend_Currency_Allow'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Currency_Base',
        'Mage_Backend_Model_Config_Backend_Currency_Base'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Currency_Cron',
        'Mage_Backend_Model_Config_Backend_Currency_Cron'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Currency_Default',
        'Mage_Backend_Model_Config_Backend_Currency_Default'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Customer_Address_Street',
        'Mage_Customer_Model_Config_Backend_Address_Street'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Customer_Password_Link_Expirationperiod',
        'Mage_Customer_Model_Config_Backend_Password_Link_Expirationperiod'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Address',
        'Mage_Customer_Model_Config_Backend_Show_Address'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Customer',
        'Mage_Customer_Model_Config_Backend_Show_Customer'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Design_Exception',
        'Mage_Backend_Model_Config_Backend_Design_Exception'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Email_Address',
        'Mage_Backend_Model_Config_Backend_Email_Address'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Email_Logo',
        'Mage_Backend_Model_Config_Backend_Email_Logo'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Email_Sender',
        'Mage_Backend_Model_Config_Backend_Email_Sender'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Image_Adapter',
        'Mage_Backend_Model_Config_Backend_Image_Adapter'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon',
        'Mage_Backend_Model_Config_Backend_Image_Favicon'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Image_Pdf',
        'Mage_Backend_Model_Config_Backend_Image_Pdf'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Locale_Timezone',
        'Mage_Backend_Model_Config_Backend_Locale_Timezone'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Log_Cron',
        'Mage_Backend_Model_Config_Backend_Log_Cron'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Price_Scope'),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Product_Alert_Cron',
        'Mage_Cron_Model_Config_Backend_Product_Alert'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Seo_Product',
        'Mage_Catalog_Model_Config_Backend_Seo_Product'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array',
        'Mage_Backend_Model_Config_Backend_Serialized_Array'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Shipping_Tablerate',
        'Mage_Shipping_Model_Config_Backend_Tablerate'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Sitemap_Cron',
        'Mage_Cron_Model_Config_Backend_Sitemap'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Storage_Media_Database',
        'Mage_Backend_Model_Config_Backend_Storage_Media_Database'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Baseurl',
        'Mage_Backend_Model_Config_Backend_Baseurl'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Cache',
        'Mage_Backend_Model_Config_Backend_Cache'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Category',
        'Mage_Catalog_Model_Config_Backend_Category'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Cookie',
        'Mage_Backend_Model_Config_Backend_Cookie'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Datashare',
        'Mage_Backend_Model_Config_Backend_Datashare'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Encrypted',
        'Mage_Backend_Model_Config_Backend_Encrypted'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_File',
        'Mage_Backend_Model_Config_Backend_File'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Filename',
        'Mage_Backend_Model_Config_Backend_Filename'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Image',
        'Mage_Backend_Model_Config_Backend_Image'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Locale',
        'Mage_Backend_Model_Config_Backend_Locale'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Secure',
        'Mage_Backend_Model_Config_Backend_Secure'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Serialized',
        'Mage_Backend_Model_Config_Backend_Serialized'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Sitemap',
        'Mage_Sitemap_Model_Config_Backend_Priority'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Store',
        'Mage_Backend_Model_Config_Backend_Store'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Backend_Translate',
        'Mage_Backend_Model_Config_Backend_Translate'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Clone_Media_Image',
        'Mage_Catalog_Model_Config_Clone_Media_Image'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Admin_Page',
        'Mage_Backend_Model_Config_Source_Admin_Page'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Type',
        'Mage_CatalogSearch_Model_Config_Source_Search_Type'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_GridPerPage',
        'Mage_Catalog_Model_Config_Source_GridPerPage'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode',
        'Mage_Catalog_Model_Config_Source_ListMode'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListPerPage',
        'Mage_Catalog_Model_Config_Source_ListPerPage'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort',
        'Mage_Catalog_Model_Config_Source_ListSort'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Catalog_TimeFormat',
        'Mage_Catalog_Model_Config_Source_TimeFormat'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Enabled',
        'Mage_Cms_Model_Config_Source_Wysiwyg_Enabled'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Cms_Page',
        'Mage_Cms_Model_Config_Source_Page'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Country_Full',
        'Mage_Directory_Model_Config_Source_Country_Full'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency',
        'Mage_Cron_Model_Config_Source_Frequency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Currency_Service',
        'Mage_Backend_Model_Config_Source_Currency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Customer_Address_Type',
        'Mage_Customer_Model_Config_Source_Address_Type'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Customer_Group_Multiselect',
        'Mage_Customer_Model_Config_Source_Group_Multiselect'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Customer_Group',
        'Mage_Customer_Model_Config_Source_Group'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Date_Short',
        'Mage_Backend_Model_Config_Source_Date_Short'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Design_Package'),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Design_Robots',
        'Mage_Backend_Model_Config_Source_Design_Robots'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Dev_Dbautoup',
        'Mage_Backend_Model_Config_Source_Dev_Dbautoup'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Email_Identity',
        'Mage_Backend_Model_Config_Source_Email_Identity'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Email_Method',
        'Mage_Backend_Model_Config_Source_Email_Method'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Email_Smtpauth',
        'Mage_Backend_Model_Config_Source_Email_Smtpauth'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Email_Template',
        'Mage_Backend_Model_Config_Source_Email_Template'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Image_Adapter',
        'Mage_Backend_Model_Config_Source_Image_Adapter'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale_Country',
        'Mage_Backend_Model_Config_Source_Locale_Country'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale_Currency_All',
        'Mage_Backend_Model_Config_Source_Locale_Currency_All'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale_Currency',
        'Mage_Backend_Model_Config_Source_Locale_Currency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale_Timezone',
        'Mage_Backend_Model_Config_Source_Locale_Timezone'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale_Weekdays',
        'Mage_Backend_Model_Config_Source_Locale_Weekdays'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Notification_Frequency',
        'Mage_AdminNotification_Model_Config_Source_Frequency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Order_Status_New',
        'Mage_Sales_Model_Config_Source_Order_Status_New'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Order_Status_Newprocessing',
        'Mage_Sales_Model_Config_Source_Order_Status_Newprocessing'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Order_Status_Processing',
        'Mage_Sales_Model_Config_Source_Order_Status_Processing'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Order_Status',
        'Mage_Sales_Model_Config_Source_Order_Status'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Payment_Allmethods',
        'Mage_Payment_Model_Config_Source_Allmethods'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Payment_Allowedmethods',
        'Mage_Payment_Model_Config_Source_Allowedmethods'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Payment_Allspecificcountries',
        'Mage_Payment_Model_Config_Source_Allspecificcountries'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Payment_Cctype',
        'Mage_Payment_Model_Config_Source_Cctype'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Price_Scope',
        'Mage_Catalog_Model_Config_Source_Price_Scope'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Price_Step',
        'Mage_Catalog_Model_Config_Source_Price_Step'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price',
        'Mage_Catalog_Model_Config_Source_Product_Options_Price'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type',
        'Mage_Catalog_Model_Config_Source_Product_Options_Type'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Product_Thumbnail',
        'Mage_Catalog_Model_Config_Source_Product_Thumbnail'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Reports_Scope',
        'Mage_Backend_Model_Config_Source_Reports_Scope'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods',
        'Mage_Shipping_Model_Config_Source_Allmethods'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Allspecificcountries',
        'Mage_Shipping_Model_Config_Source_Allspecificcountries'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Flatrate',
        'Mage_Shipping_Model_Config_Source_Flatrate'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Tablerate',
        'Mage_Shipping_Model_Config_Source_Tablerate'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Shipping_Taxclass',
        'Mage_Shipping_Model_Config_Source_Taxclass'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Database',
        'Mage_Backend_Model_Config_Source_Storage_Media_Database'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage',
        'Mage_Backend_Model_Config_Source_Storage_Media_Storage'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Tax_Apply_On',
        'Mage_Tax_Model_Config_Source_Apply_On'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon',
        'Mage_Tax_Model_Config_Source_Basedon'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Tax_Catalog',
        'Mage_Tax_Model_Config_Source_Catalog'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Watermark_Position',
        'Mage_Catalog_Model_Config_Source_Watermark_Position'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Web_Protocol',
        'Mage_Backend_Model_Config_Source_Web_Protocol'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Web_Redirect',
        'Mage_Backend_Model_Config_Source_Web_Redirect'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Allregion',
        'Mage_Directory_Model_Config_Source_Allregion'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Category',
        'Mage_Catalog_Model_Config_Source_Category'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Checktype',
        'Mage_Backend_Model_Config_Source_Checktype'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Country',
        'Mage_Directory_Model_Config_Source_Country'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Currency',
        'Mage_Backend_Model_Config_Source_Currency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Enabledisable',
        'Mage_Backend_Model_Config_Source_Enabledisable'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Frequency',
        'Mage_Sitemap_Model_Config_Source_Frequency'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Locale',
        'Mage_Backend_Model_Config_Source_Locale'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Nooptreq',
        'Mage_Backend_Model_Config_Source_Nooptreq'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Store',
        'Mage_Backend_Model_Config_Source_Store'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Website',
        'Mage_Backend_Model_Config_Source_Website'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Yesno', 'Mage_Backend_Model_Config_Source_Yesno'),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Config_Source_Yesnocustom',
        'Mage_Backend_Model_Config_Source_Yesnocustom'
    ),
    $this->_getClassRule('Mage_Adminhtml_Model_System_Store', 'Mage_Core_Model_System_Store'),
    $this->_getClassRule('Mage_Adminhtml_Model_Url', 'Mage_Backend_Model_Url'),
    $this->_getClassRule('Mage_Adminhtml_Rss_CatalogController'),
    $this->_getClassRule('Mage_Adminhtml_Rss_OrderController'),
    $this->_getClassRule('Mage_Adminhtml_SystemController', 'Mage_Backend_Adminhtml_SystemController'),
    $this->_getClassRule('Mage_Adminhtml_System_ConfigController', 'Mage_Backend_Adminhtml_System_ConfigController'),
    $this->_getClassRule('Mage_Bundle_Product_EditController', 'Mage_Bundle_Adminhtml_Bundle_SelectionController'),
    $this->_getClassRule('Mage_Bundle_SelectionController', 'Mage_Bundle_Adminhtml_Bundle_SelectionController'),
    $this->_getClassRule('Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image'),
    $this->_getClassRule('Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Image'),
    $this->_getClassRule('Mage_Catalog_Model_Resource_Product_Attribute_Frontend_Tierprice'),
    $this->_getClassRule('Mage_Core_Block_Flush'),
    $this->_getClassRule('Mage_Core_Block_Template_Facade'),
    $this->_getClassRule('Mage_Core_Controller_Varien_Router_Admin', 'Mage_Backend_Controller_Router_Default'),
    $this->_getClassRule('Mage_Core_Model_Config_System'),
    $this->_getClassRule('Mage_Core_Model_Design_Source_Apply'),
    $this->_getClassRule('Mage_Core_Model_Language'),
    $this->_getClassRule('Mage_Core_Model_Resource_Language'),
    $this->_getClassRule('Mage_Core_Model_Resource_Language_Collection'),
    $this->_getClassRule('Mage_Core_Model_Session_Abstract_Varien'),
    $this->_getClassRule('Mage_Core_Model_Session_Abstract_Zend'),
    $this->_getClassRule('Mage_Core_Model_Layout_Data', 'Mage_Core_Model_Layout_Update'),
    $this->_getClassRule('Mage_Customer_Block_Account'),
    $this->_getClassRule('Mage_Directory_Model_Resource_Currency_Collection'),
    $this->_getClassRule('Mage_Downloadable_FileController', 'Mage_Downloadable_Adminhtml_Downloadable_FileController'),
    $this->_getClassRule('Mage_Downloadable_Product_EditController', 'Mage_Adminhtml_Catalog_ProductController'),
    $this->_getClassRule('Mage_GiftMessage_Block_Message_Form'),
    $this->_getClassRule('Mage_GiftMessage_Block_Message_Helper'),
    $this->_getClassRule('Mage_GiftMessage_IndexController'),
    $this->_getClassRule('Mage_GiftMessage_Model_Entity_Attribute_Backend_Boolean_Config'),
    $this->_getClassRule('Mage_GiftMessage_Model_Entity_Attribute_Source_Boolean_Config'),
    $this->_getClassRule('Mage_GoogleOptimizer_IndexController',
        'Mage_GoogleOptimizer_Adminhtml_Googleoptimizer_IndexController'),
    $this->_getClassRule('Mage_Ogone_Model_Api_Debug'),
    $this->_getClassRule('Mage_Ogone_Model_Resource_Api_Debug'),
    $this->_getClassRule('Mage_Page_Block_Html_Toplinks'),
    $this->_getClassRule('Mage_Page_Block_Html_Wrapper'),
    $this->_getClassRule('Mage_Poll_Block_Poll'),
    $this->_getClassRule('Mage_ProductAlert_Block_Price'),
    $this->_getClassRule('Mage_ProductAlert_Block_Stock'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Coupons_Collection'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Invoiced_Collection'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Product_Ordered_Collection'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Product_Viewed_Collection',
        'Mage_Reports_Model_Resource_Report_Product_Viewed_Collection'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Refunded_Collection'),
    $this->_getClassRule('Mage_Reports_Model_Resource_Shipping_Collection'),
    $this->_getClassRule('Mage_Rss_Model_Observer'),
    $this->_getClassRule('Mage_Rss_Model_Session', 'Mage_Backend_Model_Auth and Mage_Backend_Model_Auth_Session'),
    $this->_getClassRule('Mage_Sales_Block_Order_Details'),
    $this->_getClassRule('Mage_Sales_Block_Order_Tax'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Address'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Address_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Attribute_Backend_Child'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Attribute_Backend_Parent'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Child'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Attribute_Backend_Parent'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Comment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Comment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Creditmemo_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Child'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Order'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Parent'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Comment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Comment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Invoice_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Payment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Payment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Child'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Attribute_Backend_Parent'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Comment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Comment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Track'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Shipment_Track_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Status_History'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Order_Status_History_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Child'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Parent'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Region'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Custbalance'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Grand'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Shipping'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Subtotal'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Tax'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Rate'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Address_Rate_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Item'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Item_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Payment'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Quote_Payment_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Sale_Collection'),
    $this->_getClassRule('Mage_Sales_Model_Entity_Setup'),
    $this->_getClassRule('Mage_Shipping_ShippingController'),
    $this->_getClassRule('Mage_Tag_Block_Customer_Edit'),
    $this->_getClassRule('Mage_User_Model_Roles'),
    $this->_getClassRule('Mage_User_Model_Resource_Roles'),
    $this->_getClassRule('Mage_User_Model_Resource_Roles_Collection'),
    $this->_getClassRule('Mage_User_Model_Resource_Roles_User_Collection'),
    $this->_getClassRule('Mage_Wishlist_Model_Resource_Product_Collection'),
    $this->_getClassRule('Mage_XmlConnect_Helper_Payment'),
    $this->_getClassRule('Varien_Convert_Action'),
    $this->_getClassRule('Varien_Convert_Action_Abstract'),
    $this->_getClassRule('Varien_Convert_Action_Interface'),
    $this->_getClassRule('Varien_Convert_Adapter_Abstract'),
    $this->_getClassRule('Varien_Convert_Adapter_Db_Table'),
    $this->_getClassRule('Varien_Convert_Adapter_Http'),
    $this->_getClassRule('Varien_Convert_Adapter_Http_Curl'),
    $this->_getClassRule('Varien_Convert_Adapter_Interface'),
    $this->_getClassRule('Varien_Convert_Adapter_Io'),
    $this->_getClassRule('Varien_Convert_Adapter_Soap'),
    $this->_getClassRule('Varien_Convert_Adapter_Std'),
    $this->_getClassRule('Varien_Convert_Adapter_Zend_Cache'),
    $this->_getClassRule('Varien_Convert_Adapter_Zend_Db'),
    $this->_getClassRule('Varien_Convert_Container_Collection'),
    $this->_getClassRule('Varien_Convert_Container_Generic'),
    $this->_getClassRule('Varien_Convert_Container_Interface'),
    $this->_getClassRule('Varien_Convert_Mapper_Abstract'),
    $this->_getClassRule('Varien_Convert_Parser_Abstract'),
    $this->_getClassRule('Varien_Convert_Parser_Csv'),
    $this->_getClassRule('Varien_Convert_Parser_Interface'),
    $this->_getClassRule('Varien_Convert_Parser_Serialize'),
    $this->_getClassRule('Varien_Convert_Parser_Xml_Excel'),
    $this->_getClassRule('Varien_Convert_Profile'),
    $this->_getClassRule('Varien_Convert_Profile_Abstract'),
    $this->_getClassRule('Varien_Convert_Profile_Collection'),
    $this->_getClassRule('Varien_Convert_Validator_Abstract'),
    $this->_getClassRule('Varien_Convert_Validator_Column'),
    $this->_getClassRule('Varien_Convert_Validator_Dryrun'),
    $this->_getClassRule('Varien_Convert_Validator_Interface'),
    $this->_getClassRule('Varien_File_Uploader_Image'),
    $this->_getClassRule('Varien_Profiler', 'Magento_Profiler'),
);
