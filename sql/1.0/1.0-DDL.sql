
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
	
	`lastActive`	DATETIME	NOT NULL, 
	`lastIP`		VARCHAR(64)	NOT NULL, 
	
	`staff`			BIT			NOT NULL DEFAULT 0,
	`mod`			BIT			NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
	`id`			BIGINT(20)		UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`userId`		INT(10)			UNSIGNED NOT NULL, 
	`userIP`		VARCHAR(64)		NOT NULL, 
	`hash`			CHAR(128)		NOT NULL UNIQUE, 
	`startDate`		DATETIME		NOT NULL, 
	`active`		BIT				NOT NULL DEFAULT 1,
	
	PRIMARY KEY (`id`), 
	FOREIGN KEY (`userId`) REFERENCES `user_accounts` (`id`)
) ENGINE=InnoDB;

