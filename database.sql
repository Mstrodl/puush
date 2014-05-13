CREATE TABLE IF NOT EXISTS `puush` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `name` text NOT NULL,
  `ext` varchar(5) NOT NULL,
  `view` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=495 ;