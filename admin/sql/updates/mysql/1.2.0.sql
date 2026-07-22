ALTER TABLE `#__hwdlinks_link`
ADD COLUMN `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Modified by user id' AFTER `rgt`,
ADD COLUMN `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the link was modified.' AFTER `created_by`;
