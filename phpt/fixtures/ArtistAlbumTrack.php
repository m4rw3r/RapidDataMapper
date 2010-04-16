<?php

$db = Rdm_Adapter::getInstance();

$db->query('DROP TABLE IF EXISTS `albums`');
$db->query('DROP TABLE IF EXISTS `artists`');
$db->query('DROP TABLE IF EXISTS `tracks`');

$db->query('CREATE TABLE `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `artist_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artist_id` (`artist_id`)
)');

$db->query('CREATE TABLE `artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE `tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artist_id` (`artist_id`),
  KEY `album_id` (`album_id`)
)');


$db->query("INSERT INTO `artists` (`id`,`name`)
VALUES
	(1,'Draconian'),
	(2,'Cult of Luna')");

$db->query("INSERT INTO `albums` (`id`,`name`,`artist_id`)
VALUES
	(1,'Turning Season Within',1),
	(2,'Where Lovers Mourn',1),
	(3,'Eternal Kingdom',2)");

$db->query("INSERT INTO `tracks` (`id`,`name`,`artist_id`,`album_id`)
VALUES
	(1,'Seasons Apart',1,1),
	(2,'When I Wake',1,1),
	(3,'Earthbound',1,1),
	(4,'Not Breathing',1,1),
	(5,'The Failure Epiphany',1,1),
	(6,'Morphine Cloud',1,1),
	(7,'Bloodflower',1,1),
	(8,'The Empty Stare',1,1),
	(9,'September Ashes',1,1),
	(10,'Owlwood',2,3),
	(11,'Eternal Kingdom',2,3),
	(12,'Ghost Trail',2,3),
	(13,'The Lure (Interlude)',2,3),
	(14,'Mire Deep',2,3),
	(15,'The Great Migration',2,3),
	(16,'Österbotten',2,3),
	(17,'Curse',2,3),
	(18,'Ugin',2,3),
	(19,'Following Betulas',2,3),
	(20,'The Cry of Silence',1,2),
	(21,'Silent Winter',1,2),
	(22,'A Slumber Did My Spirit Seal',1,2),
	(23,'The Solitude',1,2),
	(24,'Reversio Ad Secessum',1,2),
	(25,'The Amaranth',1,2),
	(26,'Akherousia',1,2),
	(27,'It Grieves My Heart',1,2)");

// DO NOT LET THE DB CONNECTION AFFECT THE REST OF THE TEST:
unset($db);
