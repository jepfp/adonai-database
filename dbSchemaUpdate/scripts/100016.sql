CREATE TABLE `file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lied_id` bigint(20) NOT NULL,
  `data` mediumblob NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filesize` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `filetype` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fkLied_idx` (`lied_id`),
  CONSTRAINT `fkLied` FOREIGN KEY (`lied_id`) REFERENCES `lied` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
;