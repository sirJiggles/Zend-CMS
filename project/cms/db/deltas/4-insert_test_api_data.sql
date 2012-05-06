INSERT INTO `api` VALUES(1, 'system', 1, 'KSw9wKhdwj');
INSERT INTO `api` VALUES(2, 'public', 2, 'dkjYjbedoK');

--//@UNDO

DELETE FROM `api` WHERE `id` = 1 AND `id` = 2;

--//