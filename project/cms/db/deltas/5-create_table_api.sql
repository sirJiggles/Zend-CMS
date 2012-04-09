CREATE TABLE `api` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref` varchar(500) COLLATE utf8_bin NOT NULL,
  `type` tinyint(4) COLLATE utf8_bin DEFAULT NULL,
  `key` varchar(500) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3;

--//@UNDO

DROP TABLE `api`;

--//