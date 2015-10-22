
DROP DATABASE IF EXISTS `stlmodernmopar`;

CREATE DATABASE `stlmodernmopar` 
	DEFAULT CHARACTER SET utf8 
	DEFAULT COLLATE utf8_general_ci;
	
USE `stlmodernmopar`;


DROP TABLE IF EXISTS `user_accounts`;
CREATE TABLE `user_accounts` (
	`id`			INT(10)		UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`username`		VARCHAR(15)	NOT NULL, 
	`password`		CHAR(128)	NOT NULL, 
	`salt`			CHAR(128)	NOT NULL, 
	
	`state`			VARCHAR(2)	NOT NULL, 
	`city`			VARCHAR(50)	NOT NULL, 
	
	`creationDate`	DATETIME	NOT NULL, 
	`creationIP`	VARCHAR(64)	NOT NULL,
	
	`lastIP`		VARCHAR(64)	NOT NULL, 
	
	`staff`			BIT			NOT NULL DEFAULT 0,
	`mod`			BIT			NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;




