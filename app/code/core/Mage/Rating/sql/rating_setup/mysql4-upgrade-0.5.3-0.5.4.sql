SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `rating` 
    DROP FOREIGN KEY `FK_RATING_ENTITY`;

ALTER TABLE `rating` CHANGE `entity_id` `entity_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `rating` 
    ADD CONSTRAINT `FK_RATING_ENTITY_KEY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
