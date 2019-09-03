DROP TABLE IF EXISTS `#__helloworld`;

CREATE TABLE `#__helloworld` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`greeting` VARCHAR(25) NOT NULL,
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`email` VARCHAR(50) NOT NULL ,`mobile` VARCHAR(50) NOT NULL ,
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__helloworld` (`greeting`,`email`,`mobile`) VALUES
('Hello World from database!','abc@gmail.com','9874563210'),
('Good bye World from database!','abcd@gmail.com','1234569870');