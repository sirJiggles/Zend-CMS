INSERT INTO `content-type-fields` VALUES(1, 1, 'image', 'image');
INSERT INTO `content-type-fields` VALUES(2, 1, 'title', 'text');
INSERT INTO `content-type-fields` VALUES(3, 1, 'content', 'wysiwyg');
INSERT INTO `content-type-fields` VALUES(4, 2, 'title', 'text');
INSERT INTO `content-type-fields` VALUES(5, 2, 'image', 'image');

--//@UNDO

DELETE FROM `content-types` WHERE `id` = 1 AND `id` = 2 AND `id` = 3 AND `id` = 4 AND `id` = 5;

--//