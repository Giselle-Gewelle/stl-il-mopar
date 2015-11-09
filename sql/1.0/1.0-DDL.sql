
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
	
	`posts`			INT(10)		NOT NULL DEFAULT 0,
	`threads`		INT(10)		NOT NULL DEFAULT 0,
	
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


DROP TABLE IF EXISTS `user_passwordChanges`;
CREATE TABLE `user_passwordChanges` (
	`userId`		INT(10)			UNSIGNED NOT NULL,
	`date`			DATETIME		NOT NULL, 
	`ip`			VARCHAR(64)		NOT NULL, 
	`oldHash`		CHAR(128)		NOT NULL, 
	`oldSalt`		CHAR(128)		NOT NULL, 
	`newHash`		CHAR(128)		NOT NULL, 
	`newSalt`		CHAR(128)		NOT NULL, 
	
	PRIMARY KEY (`userId`, `date`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `forum_lists`;
CREATE TABLE `forum_lists` (
	`id`			SMALLINT(5)		UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`position`		SMALLINT(5)		UNSIGNED NOT NULL,
	`name`			VARCHAR(50)		NOT NULL,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `forum_forums`;
CREATE TABLE `forum_forums` (
	`id`			SMALLINT(5)		UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`listId`		SMALLINT(5)		UNSIGNED NOT NULL, 
	`position`		TINYINT(3)		UNSIGNED NOT NULL, 
	`name`			VARCHAR(50)		NOT NULL, 
	`description`	VARCHAR(150)	NOT NULL, 
	
	`threads`		INT(10)			UNSIGNED NOT NULL DEFAULT 0,
	`posts`			INT(10)			UNSIGNED NOT NULL DEFAULT 0,
	
	`lastPostDate`	DATETIME		NULL, 
	`lastPoster`	VARCHAR(15)		NULL, 
	`lastPosterId`	INT(10)			UNSIGNED NULL, 
	`lastPostId`	INT(10)			UNSIGNED NULL, 
	`lastThread`	VARCHAR(50)		NULL, 
	`lastThreadId`	INT(10)			UNSIGNED NULL,
	
	`locked`		BIT				NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`),
	FOREIGN KEY (`listId`) REFERENCES `forum_lists` (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `forum_threads`;
CREATE TABLE `forum_threads` (
	`id`			INT(10)			UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE, 
	`forumId`		SMALLINT(5)		UNSIGNED NOT NULL, 
	`title`			VARCHAR(50)		NOT NULL, 
	`author`		VARCHAR(15)		NOT NULL, 
	`authorId`		INT(10)			UNSIGNED NOT NULL, 
	`authorIP`		VARCHAR(64)		NOT NULL,
	`date`			DATETIME		NOT NULL, 
	`posts`			INT(10)			UNSIGNED NOT NULL DEFAULT 1, 
	
	`lastPostDate`	DATETIME		NOT NULL, 
	`lastPoster`	VARCHAR(15)		NOT NULL, 
	`lastPosterId`	INT(10)			UNSIGNED NOT NULL, 
	`lastPosterIP`	VARCHAR(64)		NOT NULL, 
	
	`locked`		BIT				NOT NULL DEFAULT 0,
	`hidden`		BIT				NOT NULL DEFAULT 0,
	`sticky`		BIT				NOT NULL DEFAULT 0,
	`autoHide`		BIT				NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`),
	FOREIGN KEY (`forumId`) REFERENCES `forum_forums` (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `forum_posts`;
CREATE TABLE `forum_posts` (
	`id`			INT(10)			UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	`threadId`		INT(10)			UNSIGNED NOT NULL, 
	`authorId`		INT(10)			UNSIGNED NOT NULL, 
	`authorIP`		VARCHAR(64)		NOT NULL,
	`date`			DATETIME		NOT NULL, 
	`message`		TEXT			NOT NULL, 
	
	`lastEditDate`	DATETIME		NULL,
	`lastEditor`	VARCHAR(15)		NULL, 
	`lastEditorId`	INT(10)			UNSIGNED NULL,
	`lastEditorIP`	VARCHAR(64)		NULL, 
	
	`hidden`		BIT				NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`), 
	FOREIGN KEY (`threadId`) REFERENCES `forum_threads` (`id`)
) ENGINE=InnoDB;



DROP TABLE IF EXISTS `forum_reports`;
CREATE TABLE `forum_reports` (
	`id`			INT(10)			UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	`type`			ENUM(
						'THREAD',
						'POST'
					)				NOT NULL, 
	`date`			DATETIME		NOT NULL, 
	`reason`		TEXT			NOT NULL, 
	`threadId`		INT(10)			UNSIGNED NOT NULL, 
	`postId`		INT(10)			UNSIGNED NULL, 
	
	`reporteeId`	INT(10)			UNSIGNED NOT NULL,
	`reporteeIP`	VARCHAR(64)		NOT NULL, 
	`reporterId`	INT(10)			UNSIGNED NOT NULL, 
	`reporterIP`	VARCHAR(64)		NOT NULL, 
	
	`lock`			BIT				NOT NULL DEFAULT 0,
	`hide`			BIT				NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;