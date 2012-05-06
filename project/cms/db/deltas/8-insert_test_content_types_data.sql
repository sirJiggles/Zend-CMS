INSERT INTO `content-types` VALUES(1, 'article');
INSERT INTO `content-types` VALUES(2, 'content');

--//@UNDO

DELETE FROM `content-types` WHERE `id` = 1 AND `id` = 2;

--//