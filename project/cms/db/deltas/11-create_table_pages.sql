CREATE TABLE  `pages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `name` VARCHAR( 500 ) NOT NULL ,
    `template` INT( 10 ) NOT NULL ,
    `content_assigned` VARCHAR( 1000 ) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3;

--//@UNDO

DROP TABLE `pages`;

--//