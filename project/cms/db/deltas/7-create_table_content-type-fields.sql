CREATE TABLE `content-type-fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_type` int(11) COLLATE utf8_bin NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `format` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3;

--//@UNDO

DROP TABLE `content-type-fields`;

--//