DROP TABLE IF EXISTS `spf_tags`;

CREATE TABLE `spf_tags` (
  `spf_id` int(11) NOT NULL DEFAULT 0,
  `tags_id` int(11) NOT NULL,
  KEY `spf_id` (`spf_id`),
  KEY `tags_id` (`tags_id`),
  CONSTRAINT `spf_tags_ibfk_1` FOREIGN KEY (`spf_id`) REFERENCES `SPF` (`id`),
  CONSTRAINT `spf_tags_ibfk_2` FOREIGN KEY (`tags_id`) REFERENCES `tags` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
