        
        ALTER TABLE  `jmmegamenu` ADD `menugroup` INT(11) UNSIGNED NOT NULL  AFTER `showtitle`;

        CREATE TABLE IF NOT EXISTS `jmmegamenu_store_menugroup` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `store_id` int(11) unsigned NOT NULL,
		  `menugroupid` int(11) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `store_id` (`store_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


         
        CREATE TABLE IF NOT EXISTS `jmmegamenu_types` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `menutype` varchar(75) NOT NULL DEFAULT '',
		  `title` varchar(255) NOT NULL DEFAULT '',
		  `description` varchar(255) NOT NULL DEFAULT '',
		  `storeid` int(10) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `menutype` (`menutype`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;
