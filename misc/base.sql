CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `offender` varchar(100) DEFAULT NULL,
  `victim` varchar(100) NOT NULL,
  `link` text NOT NULL,
  `pic` TEXT NULL DEFAULT NULL,
  `site` varchar(253) NOT NULL,
  `comment` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `index_o` (`offender`),
  KEY `index_v` (`victim`),
  KEY `index_s` (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `comments` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entry_id` INT(10) UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  INDEX `index_ei` (`entry_id`),
  CONSTRAINT `FK_entry_comment` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
