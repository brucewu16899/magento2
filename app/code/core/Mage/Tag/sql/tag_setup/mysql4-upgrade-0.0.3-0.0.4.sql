/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.22 : Database - magento_misha
*********************************************************************
Server version : 4.1.22
*/


SET NAMES utf8;

SET SQL_MODE='';
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tag_relations`;
DROP TABLE IF EXISTS `tag_relation`;
DROP TABLE IF EXISTS `tag_entity`;
DROP TABLE IF EXISTS `tag`;

/*Table structure for table `tag` */

CREATE TABLE `tag` (
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `status` smallint(6) unsigned NOT NULL default '0',
  `store_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `tag_relations` */

CREATE TABLE `tag_relation` (
  `tag_relation_id` int(11) unsigned NOT NULL auto_increment,
  `tag_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  `store_id` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  USING BTREE (`tag_relation_id`),
  KEY `FK_TAG_RELATION_TAG` (`tag_id`),
  KEY `FK_TAG_RELATION_CUSTOMER` (`customer_id`),
  KEY `FK_TAG_RELATION_PRODUCT` (`product_id`),
  KEY `FK_TAG_RELATION_STORE` (`store_id`),
  CONSTRAINT `tag_relation_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE CASCADE,
  CONSTRAINT `tag_relation_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `tag_relation_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `tag_relation_ibfk_4` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tag` (`tag_id`, `name`, `status`, `store_id`) VALUES (1, 'test', 1, 1), (2, 'good', 1, 1), (3, 'bad', 1, 1), (4, 'super', 1, 1);

INSERT INTO `tag_relation` (`tag_relation_id`, `tag_id`, `customer_id`, `product_id`, `store_id`) VALUES (1, 1, 3333, 2333, 1);
INSERT INTO `tag_relation` (`tag_relation_id`, `tag_id`, `customer_id`, `product_id`, `store_id`) VALUES (2, 1, 3333, 2334, 1);
INSERT INTO `tag_relation` (`tag_relation_id`, `tag_id`, `customer_id`, `product_id`, `store_id`) VALUES (3, 1, 3333, 2335, 1);
INSERT INTO `tag_relation` (`tag_relation_id`, `tag_id`, `customer_id`, `product_id`, `store_id`) VALUES (4, 2, 3333, 2333, 1);

SET SQL_MODE=@OLD_SQL_MODE;