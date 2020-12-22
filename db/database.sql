/*
SQLyog Ultimate v8.55 
MySQL - 5.1.49-1ubuntu8.1-log : Database - rapidshara
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`rapidshara` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `rapidshara`;

/*Table structure for table `alerts` */

DROP TABLE IF EXISTS `alerts`;

CREATE TABLE `alerts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `message` text,
  `parameter1` char(100) DEFAULT NULL,
  `parameter2` char(100) DEFAULT NULL,
  `parameter3` char(100) DEFAULT NULL,
  `parameter4` char(100) DEFAULT NULL,
  `ip` char(15) DEFAULT NULL,
  `user_agent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `alerts` */

/*Table structure for table `configuration` */

DROP TABLE IF EXISTS `configuration`;

CREATE TABLE `configuration` (
  `variable` char(30) NOT NULL DEFAULT '',
  `value` char(30) DEFAULT NULL,
  `description` char(60) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`variable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `configuration` */

insert  into `configuration`(`variable`,`value`,`description`,`comment`) values ('days_after_delete','0','Lifetime of file after delete',NULL),('days_after_download','10','Lifetime of file after download',NULL),('days_after_restore','0','Lifetime of file after restore',NULL),('days_after_upload','10','Lifetime of file after upload',NULL),('download_ranges','0','Download via ranges',NULL),('download_readfile','0','Download via readfile()',NULL),('download_xsendfile','1','Download X-Sendfile',NULL),('email','info@rapidshara.ru','Mail for info',NULL),('file_change','0','Allow or Disallow file change','Изменение файлов верменно приостановлено! RAPIDSHARA ушла обновлятся!'),('file_delete','1','Allow or Disallow file delete','Прилетело НЛО и заблокировало удаление файлов'),('file_download','0','Allow or Disallow file download','Мы закрылись. Всем спасибо. Rapidshara Team.'),('file_remove','1','Allow or Disallow file remove','Прилетело НЛО и заблокировало окончательное удаление файлов'),('file_restore','0','Allow or Disallow file restore','Прилетело НЛО и заблокировало востановление файлов'),('file_upload','0','Allow or Disallow file upload','Мы закрылись. Всем спасибо. Rapidshara Team.'),('host','rapidshara.ru','Main project host',NULL),('host_beta','beta.rapidshara.ru','Beta project host',NULL),('host_upload','r.rapidshara.ru','Host to upload file',NULL),('host_upload_beta','beta.rapidshara.ru','Beta host to upload file',NULL),('only_russians','0','Only russian','НЛО заблокировало закачку файлов для зарубежных ип'),('size','100','Size of upload file in MB',NULL),('streams','1','How much streams to download file',NULL),('user_change','1','Allow or Disallow user change','Прилетело НЛО и заблокировало изменение профиля'),('user_create','1','Allow or Disallow user create','Прилетело НЛО и заблокировало регистрацию'),('user_entry','1','Allow or Disallow user entry','Мы закрылись. Всем спасибо. Rapidshara Team.'),('user_exit','1','Allow or Disallow user exit','Прилетело НЛО и заблокировало выход'),('user_registration','0','Allow or Disallow user registration','Мы закрылись. Всем спасибо. Rapidshara Team.');

/*Table structure for table `files` */

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL DEFAULT '0',
  `Host` char(20) DEFAULT NULL,
  `Path` char(100) DEFAULT NULL,
  `Type` char(100) NOT NULL DEFAULT '',
  `Name` text NOT NULL,
  `Size` int(10) NOT NULL,
  `Description` text,
  `Password` char(15) DEFAULT NULL,
  `Keep` int(1) NOT NULL DEFAULT '0',
  `Blocked` int(1) NOT NULL DEFAULT '0',
  `Deleted` int(1) NOT NULL DEFAULT '0',
  `Date_Uploaded` timestamp NULL DEFAULT NULL,
  `Date_Download` timestamp NULL DEFAULT NULL,
  `Date_Changed` timestamp NULL DEFAULT NULL,
  `Date_Deleted` timestamp NULL DEFAULT NULL,
  `Date_Restored` timestamp NULL DEFAULT NULL,
  `IP` char(15) DEFAULT NULL,
  `Mirrors` text,
  `Redirect` char(100) DEFAULT NULL,
  `Counter` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `files` */

/*Table structure for table `hosts` */

DROP TABLE IF EXISTS `hosts`;

CREATE TABLE `hosts` (
  `name` char(30) NOT NULL,
  `path` char(30) DEFAULT NULL,
  `path_download` char(30) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `hosts` */

insert  into `hosts`(`name`,`path`,`path_download`) values ('beta.rapidshara.ru','/var/www/rapidshara.ru/rf/b','/rf/b'),('r.rapidshara.ru','/var/www/rapidshara.ru/rf/r','/rf/r');

/*Table structure for table `ips` */

DROP TABLE IF EXISTS `ips`;

CREATE TABLE `ips` (
  `ip` char(15) NOT NULL DEFAULT '',
  `upload` int(1) DEFAULT '0',
  `upload_comment` text,
  `download` int(1) DEFAULT '0',
  `download_comment` text,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `ips` */

/*Table structure for table `references` */

DROP TABLE IF EXISTS `references`;

CREATE TABLE `references` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `ip` char(15) NOT NULL,
  `blocked` int(1) DEFAULT '0',
  `blocked_commment` text,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_required` timestamp NULL DEFAULT NULL,
  `counter` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_id` (`file_id`,`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `references` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` char(100) DEFAULT NULL,
  `Email` char(50) DEFAULT NULL,
  `Status` char(20) DEFAULT NULL,
  `Password` char(50) DEFAULT NULL,
  `Size` int(4) DEFAULT '0',
  `Keep` int(1) NOT NULL DEFAULT '0',
  `Deleted` int(1) NOT NULL DEFAULT '0',
  `Date_Created` timestamp NULL DEFAULT NULL,
  `Date_Changed` timestamp NULL DEFAULT NULL,
  `Date_Deleted` timestamp NULL DEFAULT NULL,
  `Date_Entry` timestamp NULL DEFAULT NULL,
  `Date_Exit` timestamp NULL DEFAULT NULL,
  `SID` char(32) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
