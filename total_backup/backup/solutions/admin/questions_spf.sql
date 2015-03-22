DROP TABLE IF EXISTS `questions_spf`;

CREATE TABLE `questions_spf` (
  `questions_id` int(11) NOT NULL,
  `spf_id` int(11) NOT NULL,
  KEY `questions_id` (`questions_id`),
  KEY `spf_id` (`spf_id`),
  CONSTRAINT `questions_spf_ibfk_1` FOREIGN KEY (`questions_id`) REFERENCES `questions` (`id`),
  CONSTRAINT `questions_spf_ibfk_2` FOREIGN KEY (`spf_id`) REFERENCES `spf` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
