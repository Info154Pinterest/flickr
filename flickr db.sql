CREATE DATABASE `flickr` /*!40100 DEFAULT CHARACTER SET latin1 */;

CREATE TABLE `results` (
  `search` varchar(500) NOT NULL,
  `id` int(100) NOT NULL,
  `owner` varchar(100) NOT NULL,
  `secret` varchar(50) DEFAULT NULL,
  `server` int(45) DEFAULT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `ispublic` tinyint(1) NOT NULL DEFAULT '0',
  `isfriend` tinyint(1) NOT NULL DEFAULT '0',
  `isfamily` tinyint(1) NOT NULL DEFAULT '0',
  `url_n` varchar(500) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`search`,`url_n`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


SELECT * FROM flickr.results;

select 
concat("insert into flickr.results values ('",
search, "','", 
id,"','", 
owner, "', '" , 
secret, "', '" , 
server, "', '" , 
REPLACE(title, "'", ""),"','",
ispublic ,"','",
isfriend , "','", 
isfamily , "','", 
url_n, "','", 
last_update, "');")
from flickr.results
;

