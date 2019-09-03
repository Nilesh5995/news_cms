DROP TABLE IF EXISTS `#__school`;

CREATE TABLE `#__school` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`fname`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`mname`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`lname`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`class`  VARCHAR(25)  NOT NULL DEFAULT '',
	`address`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`city`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`state`  VARCHAR(1024)  NOT NULL DEFAULT '',
	`pincode`  INT(25)  NOT NULL DEFAULT '0',
	`pmo` INT(12)     NOT NULL DEFAULT '0',
	`image`   VARCHAR(1024) NOT NULL DEFAULT '',
	`blood_group` VARCHAR(25) NOT NULL DEFAULT '',
	`dob` DATETIME(6) NOT NULL DEFAULT '0000-00-00 00:00:00',	
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__school` (`fname`,`mname`,`lname`,`class`,`address`,`city`,`state`,`pincode`
	,`pmo`,`image`,`blood_group`,`dob`) 
VALUES ('sushma','sunil','bhole','9','Nasik','Nasik','Maharastra',1234,1236547,'','o+','2019-08-20 08:00:00'),
('ganesh','sudhakar','bhosale','9','Nasik','Nasik','Maharastra',1234,1236547,'','o+','2019-08-20 08:00:00')
