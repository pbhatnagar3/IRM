DROP TABLE IF EXISTS `questions_dspfirst`;

CREATE TABLE `questions_dspfirst` (
  `questions_id` int(11) NOT NULL,
  `dspfirst_id` int(11) NOT NULL,
  KEY `questions_id` (`questions_id`),
  KEY `dspfirst_id` (`dspfirst_id`),
  CONSTRAINT `questions_dspfirst_ibfk_1` FOREIGN KEY (`questions_id`) REFERENCES `questions` (`id`),
  CONSTRAINT `questions_dspfirst_ibfk_2` FOREIGN KEY (`dspfirst_id`) REFERENCES `dspfirst` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
