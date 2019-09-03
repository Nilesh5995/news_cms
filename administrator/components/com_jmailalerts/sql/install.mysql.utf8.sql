--
-- Table structure for table '__jma_frequencies'
--

CREATE TABLE IF NOT EXISTS `#__jma_frequencies` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`created_by` INT(11)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(250)  NOT NULL ,
`time_measure` VARCHAR(255)  NOT NULL ,
`duration` INT(3)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table '__jma_alerts'
--

CREATE TABLE IF NOT EXISTS `#__jma_alerts` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`description` TEXT NOT NULL ,
`allow_users_select_plugins` VARCHAR(255)  NOT NULL ,
`respect_last_email_date` VARCHAR(255)  NOT NULL ,
`is_default` VARCHAR(255)  NOT NULL ,
`allowed_freq` VARCHAR(255)  NOT NULL ,
`default_freq` VARCHAR(255)  NOT NULL ,
`batch_size` int(255) NOT NULL,
`enable_batch` tinyint(1) NOT NULL DEFAULT '1',
`email_subject` VARCHAR(255)  NOT NULL ,
`template` TEXT NOT NULL ,
`template_css` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__jma_subscribers`
--

CREATE TABLE IF NOT EXISTS `#__jma_subscribers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `alert_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `frequency` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `plugins_subscribed_to` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=0;


--
-- Table structure for table '__jma_old_sync_data'
--

CREATE TABLE IF NOT EXISTS `#__jma_old_sync_data`(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date` datetime NOT NULL,
	`alert_id` int(11) NOT NULL,
	`plugin` VARCHAR(255) NOT NULL,
	`plg_data` text NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=0;
