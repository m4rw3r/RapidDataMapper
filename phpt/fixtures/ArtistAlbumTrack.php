<?php

function createArtistAlbumTrackFixture(Rdm_Adapter $db)
{
	$queries = file_get_contents(__FILE__,null,null,__COMPILER_HALT_OFFSET__);
	
	foreach(explode(';', $queries) as $q)
	{
		$q = trim($q);
		empty($q) OR $db->query($q);
	}
}

createArtistAlbumTrackFixture(Config::getAdapter());

// SQL goes below this:
__halt_compiler();

CREATE TABLE "tbl_albums" (
  "id" INTEGER PRIMARY KEY,
  "name" varchar(255) DEFAULT NULL,
  "artist_id" int(11) DEFAULT NULL
);

CREATE TABLE "tbl_artists" (
  "id" INTEGER PRIMARY KEY,
  "name" varchar(255) NOT NULL
);

CREATE TABLE "tbl_tracks" (
  "id" INTEGER PRIMARY KEY,
  "name" varchar(255) NOT NULL,
  "artist_id" int(11) DEFAULT NULL,
  "album_id" int(11) DEFAULT NULL
);


INSERT INTO "tbl_artists" ("id", "name") VALUES (1,'Draconian');
INSERT INTO "tbl_artists" ("id", "name") VALUES (2,'Cult of Luna');

INSERT INTO "tbl_albums" ("id", "name", "artist_id") VALUES (1,'Turning Season Within',1);
INSERT INTO "tbl_albums" ("id", "name", "artist_id") VALUES (2,'Where Lovers Mourn',1);
INSERT INTO "tbl_albums" ("id", "name", "artist_id") VALUES (3,'Eternal Kingdom',2);

INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (1,'Seasons Apart',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (2,'When I Wake',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (3,'Earthbound',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (4,'Not Breathing',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (5,'The Failure Epiphany',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (6,'Morphine Cloud',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (7,'Bloodflower',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (8,'The Empty Stare',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (9,'September Ashes',1,1);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (10,'Owlwood',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (11,'Eternal Kingdom',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (12,'Ghost Trail',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (13,'The Lure (Interlude)',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (14,'Mire Deep',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (15,'The Great Migration',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (16,'Österbotten',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (17,'Curse',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (18,'Ugin',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (19,'Following Betulas',2,3);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (20,'The Cry of Silence',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (21,'Silent Winter',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (22,'A Slumber Did My Spirit Seal',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (23,'The Solitude',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (24,'Reversio Ad Secessum',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (25,'The Amaranth',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (26,'Akherousia',1,2);
INSERT INTO "tbl_tracks" ("id", "name", "artist_id", "album_id") VALUES (27,'It Grieves My Heart',1,2);