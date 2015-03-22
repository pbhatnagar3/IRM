DROP TABLE IF EXISTS `spf_images`;

CREATE TABLE `spf_images` (
  `spf_id` int(11) NOT NULL,
  `images_id` int(11) NOT NULL,
  KEY `spf_id` (`spf_id`),
  KEY `images_id` (`images_id`),
  CONSTRAINT `spf_images_ibfk_1` FOREIGN KEY (`spf_id`) REFERENCES `SPF` (`id`),
  CONSTRAINT `spf_images_ibfk_2` FOREIGN KEY (`images_id`) REFERENCES `images` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
