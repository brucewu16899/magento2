/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `core_email_template`;

CREATE TABLE `core_email_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Email templates';

/*Data for the table `core_email_template` */

insert  into `core_email_template`(`template_id`,`template_code`,`template_text`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`added_at`,`modified_at`) values (1,'New account (HTML)','Welcome <strong>{{var customer.name}}</strong>!\r\n\r\n<p>Thank you very much for creating an account.</p>\r\n\r\n<p>To officially log in when you\'re visiting our site, simply click on \"Login\" or \"My Account\" located at the top of every page, and then enter your e-mail address and the password you have chosen.</p>\r\n\r\n<p>==========================================<br/>\r\nUse the following values when prompted to log in:<br/>\r\nE-mail: {{var customer.email}}<br/>\r\nPassword: {{var customer.password}}<br/>\r\n==========================================</p>\r\n\r\nWhen you log in to your account, you will be able to do the following:<br/>\r\n\r\n* Proceed through checkout faster when making a purchase<br/>\r\n* Check the status of orders<br/>\r\n* View past orders<br/>\r\n* Make changes to your account information<br/>\r\n* Change your password<br/>\r\n* Store up to 5 alternative shipping addresses (for shipping to multiple family members and friends!)<br/>\r\n\r\nIf you have any questions about your account or any other matter, please feel free to contact us at \r\n<a href=\"mailto:magento@varien.com\">magento@varien.com</a> or by phone at 1-111-111-1111.<br/>\r\n<br/>\r\nThanks again!\r\n',2,'Welcome, {{var customer.name}}!',NULL,NULL,'2007-08-13 12:28:48','2007-08-14 00:31:28'),(2,'New order (HTML)','<strong>Dear {{var billing.name}}</strong>\r\n\r\nThanks for your order!\r\n<div class=\"content\">\r\n    <h1 class=\"page-heading\">Order #{{var order.increment_id}} ({{var order.status}})</h1>\r\n    <table cellspacing=\"0\" width=\"100%\">\r\n        <thead>\r\n            <tr>\r\n                <th style=\"width:50%;\"><h3>Billing Information</h3></th>\r\n                <th style=\"width:50%;\"><h3>Payment Method</h3></th></tr>\r\n        </thead>\r\n        <tbody>\r\n            <tr>\r\n                <td>\r\n                    <address>\r\n                        {{var order.billing_address.getFormated(\'html\')}}\r\n                    </address>\r\n                </td>\r\n                <td class=\"align-center\">\r\n                    {{var order.payment.getFormated(\'html\')}}\r\n                </td>\r\n            </tr>\r\n        </tbody>\r\n    </table>\r\n    <p></p>\r\n    <table cellspacing=\"0\" width=\"100%\">\r\n        <thead>\r\n            <tr>\r\n                <th style=\"width:50%;\"><h3>Shipping Information</h3></th>\r\n                <th style=\"width:50%;\"><h3>Shipping Method</h3></th></tr>\r\n        </thead>\r\n        <tbody>\r\n            <tr>\r\n                <td>\r\n                    <address>\r\n                        {{var order.shipping_address.getFormated(\'html\')}}\r\n                    </address>\r\n                </td>\r\n                <td class=\"align-center\">\r\n                    {{var order.shipping_description}}\r\n                </td>\r\n            </tr>\r\n        </tbody>\r\n    </table>\r\n    <p></p>\r\n    {{include template=\"email/order/items.phtml\"}}\r\n</div>',2,'New Order # {{var order.increment_id}}',NULL,NULL,'2007-08-13 12:29:52','2007-08-15 23:40:20'),(3,'New password (HTML)','<p>Dear {{var customer.name}},</p>\r\n\r\n<p>Your new password is: {{var customer.password}}</p>\r\n\r\n\r\n<p>You can change your password at any time by logging into the \"My Account\" section.</p>\r\n\r\n<p>Thank you very much.</p>\r\n\r\n<p>Your internet buddies and best friends forever,</p>\r\n\r\n<p>Magento Products Office</p>\r\n',2,'New password for {{var customer.name}}',NULL,NULL,'2007-08-13 12:30:10','2007-08-15 23:19:12'),(4,'Order update','Hello, {{var billing.firstname}}',2,'Order # {{var order.increment_id}} update',NULL,NULL,'2007-08-13 16:27:58','2007-08-13 16:28:05'),(5,'New account (Plain)','Welcome {{var customer.name}}!\r\n\r\nThank you very much for creating an account.\r\n\r\nTo officially log in when you\'re visiting our site, simply click on \"Login\" or \"My Account\" located at the top of every page, and then enter your e-mail address and the password you have chosen.\r\n\r\n==========================================\r\n\r\nUse the following values when prompted to log in:\r\n\r\nE-mail: {{var customer.email}}\r\n\r\nPassword: {{var customer.password}}\r\n\r\n==========================================\r\n\r\nWhen you log in to your account, you will be able to do the following:\r\n\r\n* Proceed through checkout faster when making a purchase\r\n\r\n* Check the status of orders\r\n\r\n* View past orders\r\n\r\n* Make changes to your account information\r\n\r\n* Change your password\r\n\r\n* Store up to 5 alternative shipping addresses (for shipping to multiple family members and friends!)\r\n\r\nIf you have any questions about your account or any other matter, please feel free to contact us at \r\nmagento@varien.com or by phone at 1-111-111-1111.\r\n\r\n\r\nThanks again!',2,'Welcome {{var customer.name}}',NULL,NULL,'2007-08-14 00:32:34','2007-08-14 00:32:34'),(6,'Newsletter subscription confirmation (HTML)','Hello,\r\n\r\nThank you for subscribing to our newsletter.\r\n\r\nTo begin receiving the newsletter, you must first confirm your subscription by clicking on the following link:\r\n\r\n<a href=\"{{var newsletter_link}}\">{{var newsletter_link}}</a>\r\n\r\nThanks again!\r\n\r\nSincerely,\r\n\r\nMagento Store.',2,'Newsletter subscription confirmation',NULL,NULL,'2007-08-16 18:31:57','2007-08-16 18:31:57');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
