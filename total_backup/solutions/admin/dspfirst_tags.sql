DROP TABLE IF EXISTS `dspfirst_tags`;

CREATE TABLE `dspfirst_tags` (
  `dspfirst_id` int(11) NOT NULL DEFAULT 0,
  `tags_id` int(11) NOT NULL,
  KEY `dspfirst_id` (`dspfirst_id`),
  KEY `tags_id` (`tags_id`),
  CONSTRAINT `dspfirst_tags_ibfk_1` FOREIGN KEY (`dspfirst_id`) REFERENCES `dspfirst` (`id`),
  CONSTRAINT `dspfirst_tags_ibfk_2` FOREIGN KEY (`tags_id`) REFERENCES `tags` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
