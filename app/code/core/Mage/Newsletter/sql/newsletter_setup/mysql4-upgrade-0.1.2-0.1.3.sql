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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `newsletter_template`;

CREATE TABLE `newsletter_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) character set latin1 collate latin1_general_ci default NULL,
  `template_text` text,
  `template_text_preprocessed` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `template_actual` tinyint(1) unsigned default '1',
  PRIMARY KEY  (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';

insert into `newsletter_template` (`template_id`,`template_code`,`template_text`,`template_text_preprocessed`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`template_actual`) values (1,'subscriberCodeConfirm','You have subscribed to Magento E-commerce Newsletter!<br />\r\n<br />\r\nTo confirm you subscription please follow this <a href=\"http://magento-mitch.kiev-dev/adminhtml/newsletter_template/{insvar%20subscriber.getConfirmationLink()}\" target=\"_blank\" title=\"Confirm subscription\">link</a>.<br />\r\n<br />\r\nThanks for your interest in our product!\r\n',NULL,2,'Confirmation of your subscription','Magento E-Commerce','core@magentocommerce.com',1);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;