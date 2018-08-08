-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` varchar(90) NOT NULL,
  `nama` text NOT NULL,
  `hak_akses` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `hak_akses`) VALUES
(1,	'kszxpo',	'319949ab1252fc41bf437c3dea2859bdd1cad966',	'VulnWalker',	'1;2;3;4'),
(2,	'SA',	'10470c3b4b1fed12c3baac014be15fac67c6e815',	'Iwan',	'1;2;3;4');

DROP TABLE IF EXISTS `history_backup`;
CREATE TABLE `history_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jam` text NOT NULL,
  `id_server` int(11) NOT NULL,
  `nama_database` text NOT NULL,
  `file_backup` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `history_backup` (`id`, `tanggal`, `jam`, `id_server`, `nama_database`, `file_backup`) VALUES
(16,	'2018-02-23',	'',	23,	'bonus_pulsa',	'{\"struktur\":\"/backup/2018-02-23-bonuspulsa-bonus_pulsa.struk.sql\",\"data\":\"/backup/2018-02-23-bonuspulsa-bonus_pulsa.data.sql\",\"triger\":\"/backup/2018-02-23-bonuspulsa-bonus_pulsa.triger.sql\"}'),
(17,	'2018-02-23',	'16:59',	23,	'bonus_pulsa',	'{\"struktur\":\"/backup/2018-02-23-16:59-bonuspulsa-bonus_pulsa.struk.sql.gz\",\"data\":\"/backup/2018-02-23-16:59-bonuspulsa-bonus_pulsa.data.sql.gz\",\"triger\":\"/backup/2018-02-23-16:59-bonuspulsa-bonus_pulsa.triger.sql.gz\",\"release\":\"/backup/2018-02-23-16:59-bonuspulsa-bonus_pulsa.gz\"}'),
(18,	'2018-02-23',	'17:01',	23,	'bonus_pulsa',	'{\"struktur\":\"/backup/2018-02-23-17:01-bonuspulsa-bonus_pulsa.struk.sql.gz\",\"data\":\"/backup/2018-02-23-17:01-bonuspulsa-bonus_pulsa.data.sql.gz\",\"triger\":\"/backup/2018-02-23-17:01-bonuspulsa-bonus_pulsa.triger.sql.gz\",\"release\":\"/backup/2018-02-23-17:01-bonuspulsa-bonus_pulsa.gz\"}'),
(19,	'2018-02-23',	'17:04',	23,	'bonus_pulsa',	'{\"struktur\":\"/backup/2018-02-23-17:04-bonuspulsa-bonus_pulsa.struk.sql.gz\",\"data\":\"/backup/2018-02-23-17:04-bonuspulsa-bonus_pulsa.data.sql.gz\",\"triger\":\"/backup/2018-02-23-17:04-bonuspulsa-bonus_pulsa.triger.sql.gz\",\"release\":\"/backup/2018-02-23-17:04-bonuspulsa-bonus_pulsa.zip\"}'),
(20,	'2018-02-23',	'17:06',	23,	'bonus_pulsa',	'{\"struktur\":\"/backup/2018-02-23-17:06-bonuspulsa-bonus_pulsa.struk.sql.gz\",\"data\":\"/backup/2018-02-23-17:06-bonuspulsa-bonus_pulsa.data.sql.gz\",\"triger\":\"/backup/2018-02-23-17:06-bonuspulsa-bonus_pulsa.triger.sql.gz\",\"release\":\"/backup/2018-02-23-17:06-bonuspulsa-bonus_pulsa.zip\"}');

DROP TABLE IF EXISTS `info_server`;
CREATE TABLE `info_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_server` int(11) NOT NULL,
  `processor` text NOT NULL,
  `kernel` text NOT NULL,
  `os` text NOT NULL,
  `ram` text NOT NULL,
  `harddisk` text NOT NULL,
  `server_status` text NOT NULL,
  `web_status` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `json_file_check`;
CREATE TABLE `json_file_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isi` longtext NOT NULL,
  `username` text NOT NULL,
  `tanggal` text NOT NULL,
  `jam` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `mysql_password`;
CREATE TABLE `mysql_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` text NOT NULL,
  `hash` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `mysql_password` (`id`, `password`, `hash`) VALUES
(1,	'rf09thebye',	'*201C6C035F56309945771D6B23382976E103BB59'),
(2,	'12345',	'*00A51F3F48415C7D4E8908980D443C29C69B60C9'),
(3,	'Admin18',	'*478D268CED5020FB751651BD316B8E925C8EA79D'),
(4,	'waso',	'*FC8CD2E05A1F10A0567E30648D678C8DB9917050'),
(5,	'hubla',	'*2C1EE93954870DE133C0151C1E011FAAF358E2B3'),
(6,	'brumbrum',	'*0B7FDA545E59789CD18A82AC8CB72F91C3C5D995');

DROP TABLE IF EXISTS `ref_dir_check`;
CREATE TABLE `ref_dir_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` text NOT NULL,
  `directory` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `ref_dir_check` (`id`, `nama`, `directory`) VALUES
(1,	'Remote Pilar',	'/home/kszxpo/public_html/ftp_fucker'),
(2,	'ATISISKADA',	'/home/kszxpo/public_html/atisiskada'),
(3,	'MAPPING',	'/home/kszxpo/public_html/mapping'),
(4,	'MAPPING PANDEGLANG',	'/home/kszxpo/public_html/pandeglang');

DROP TABLE IF EXISTS `ref_disk`;
CREATE TABLE `ref_disk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_harddisk` text NOT NULL,
  `file_system` text NOT NULL,
  `id_server` int(11) NOT NULL,
  `backup_location` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `ref_disk` (`id`, `nama_harddisk`, `file_system`, `id_server`, `backup_location`) VALUES
(3,	'Hard Disk Bonus Pulsa',	'/dev/vda1',	23,	'backup'),
(4,	'Hard Disk VulnWalker',	'/dev/vda1',	21,	'backup'),
(5,	'Haddisk Local',	'/dev/sda1',	10,	'backup');

DROP TABLE IF EXISTS `ref_release`;
CREATE TABLE `ref_release` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_release` text NOT NULL,
  `tanggal_release` text NOT NULL,
  `directory_location` text NOT NULL,
  `nama_database` text NOT NULL,
  `last_modified` text NOT NULL,
  `mysql_file` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `ref_release` (`id`, `nama_release`, `tanggal_release`, `directory_location`, `nama_database`, `last_modified`, `mysql_file`) VALUES
(10,	'chatting',	'2018-02-13',	'/var/www/chating',	'remote_server',	'2018-02-13 09:29:29',	'/var/www/chating/remote_server.sql');

DROP TABLE IF EXISTS `ref_server`;
CREATE TABLE `ref_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_server` text NOT NULL,
  `alias` text NOT NULL,
  `alamat_ip` text NOT NULL,
  `user_ftp` text NOT NULL,
  `password_ftp` text NOT NULL,
  `port_ftp` text NOT NULL,
  `user_mysql` text NOT NULL,
  `password_mysql` text NOT NULL,
  `port_mysql` text NOT NULL,
  `status` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `ref_server` (`id`, `nama_server`, `alias`, `alamat_ip`, `user_ftp`, `password_ftp`, `port_ftp`, `user_mysql`, `password_mysql`, `port_mysql`, `status`) VALUES
(21,	'Ourheart',	'ourheart',	'128.199.176.145',	'root',	'rf09thebye',	'22',	'root',	'since1945',	'3306',	''),
(23,	'Bonus Pulsa',	'bonuspulsa',	'128.199.223.159',	'root',	'Hash2856',	'22',	'root',	'since1945',	'3306',	''),
(24,	'Ceukokom',	'ceukokom',	'188.166.246.44',	'root',	'rf09thebye',	'22',	'root',	'alimrugi',	'3306',	''),
(1000,	'localhost',	'localheart',	'127.0.0.1',	'root',	'rf09thebye',	'22',	'root',	'rf09thebye',	'3306',	'');

-- 2018-03-20 01:41:53