
DROP DATABASE IF EXISTS `angularcms`;

CREATE DATABASE `angularcms` 
	DEFAULT CHARACTER SET utf8 
	DEFAULT COLLATE utf8_general_ci;
	
USE `angularcms`;


DROP TABLE IF EXISTS `user_accounts`;
CREATE TABLE `user_accounts` (
	`id`			INT(10)		UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`username`		VARCHAR(15)	NOT NULL, 
	`password`		CHAR(128)	NOT NULL, 
	`salt`			CHAR(128)	NOT NULL, 
	`dob`			DATE		NOT NULL, 
	`country`		TINYINT(3)	UNSIGNED NOT NULL,
	
	`creationDate`	DATETIME	NOT NULL, 
	`creationIP`	VARCHAR(64)	NOT NULL,
	
	`lastIP`		VARCHAR(64)	NOT NULL, 
	
	`staff`			BIT			NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;



DELIMITER $$


DROP PROCEDURE IF EXISTS `user_checkUsername` $$
CREATE PROCEDURE `user_checkUsername` (
	IN `in_username`	VARCHAR(15),
	OUT `out_count`		TINYINT(1) UNSIGNED
) 
BEGIN
	SELECT COUNT(`id`) 
		INTO `out_count` 
	FROM `user_accounts` 
	WHERE LOWER(`username`) = LOWER(`in_username`) 
	LIMIT 1;
END $$


DROP PROCEDURE IF EXISTS `user_createAccount` $$
CREATE PROCEDURE `user_createAccount` (
	IN `in_username`	VARCHAR(15),
	IN `in_passHash`	CHAR(128),
	IN `in_passSalt`	CHAR(128),
	IN `in_dob`			DATE,
	IN `in_country`		VARCHAR(3),
	IN `in_date`		DATETIME,
	IN `in_ip`			VARCHAR(64),
	OUT `out_success`	BIT
) 
BEGIN 
	INSERT INTO `user_accounts` (
		`username`, `password`, `salt`, `dob`, `country`, `creationDate`, `creationIP`, `lastIP` 
	) VALUES (
		`in_username`, `in_passHash`, `in_passSalt`, `in_dob`, `in_country`, `in_date`, `in_ip`, `in_ip`
	);
	
	IF (ROW_COUNT() > 0) THEN 
		SET `out_success` = 1;
	ELSE 
		SET `out_success` = 0;
	END IF;
END $$


DELIMITER ;




