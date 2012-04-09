INSERT INTO `users` VALUES(1, 'gfuller', '36bdf929501b3f9dcc0428edbc5832c250e43152', 'Gareth', 'Fuller', 'admin', 'gareth-fuller@hotmail.co.uk', 1,'');
INSERT INTO `users` VALUES(2, 'gfuller2', '36bdf929501b3f9dcc0428edbc5832c250e43152', 'Gareth', 'Editor', 'editor', 'gareth.fuller@studio24.net', 1,'');

--//@UNDO

DELETE FROM `user` WHERE `id` = 1 AND `id` = 2;

--//
