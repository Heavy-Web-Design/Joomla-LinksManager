ALTER TABLE `#__hwdlinks_link`
  ADD COLUMN `hide_modified` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Whether to hide the modified date on the frontend.' AFTER `browserNav`;
