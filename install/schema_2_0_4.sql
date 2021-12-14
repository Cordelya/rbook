
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `rbook`
-- 
CREATE DATABASE `rbook` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rbook`;

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

CREATE TABLE `categories` (
  `id` smallint(5) unsigned NOT NULL,
  `name` char(60) NOT NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `categories` VALUES (0, 'Barbecue', '2007-03-24 13:36:08', '2007-03-24 13:36:08');
INSERT INTO `categories` VALUES (1, 'Breads', '2007-03-24 13:36:08', '2007-03-24 13:36:08'); 
INSERT INTO `categories` VALUES (2, 'Breakfast', '2007-03-24 13:36:08', '2007-03-24 13:36:08');
INSERT INTO `categories` VALUES (3, 'Components', '2007-03-24 13:36:08', '2007-03-24 13:36:08'); 
INSERT INTO `categories` VALUES (4, 'Cookies', '2007-03-24 13:36:08', '2007-03-24 13:36:08');
INSERT INTO `categories` VALUES (5, 'Desserts', '2007-03-24 13:36:08', '2007-03-24 13:36:08'); 
INSERT INTO `categories` VALUES (6, 'Main', '2007-03-24 13:36:08', '2007-03-24 13:36:08');
INSERT INTO `categories` VALUES (7, 'Pies', '2007-03-24 13:36:08', '2007-03-24 13:36:08'); 
INSERT INTO `categories` VALUES (8, 'Salads', '2007-03-24 13:36:08', '2007-03-24 13:36:08');
INSERT INTO `categories` VALUES (9, 'Soups', '2007-03-24 13:36:08', '2007-03-24 13:36:08'); 


-- --------------------------------------------------------

-- 
-- Table structure for table `categories_seq`
-- 

CREATE TABLE `categories_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

INSERT INTO `categories_seq` VALUES (22);


-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` mediumint(8) unsigned NOT NULL,
  `comment` blob,
  `recipeid` mediumint(8) unsigned NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL,
  `rating` smallint(5) unsigned default NULL,
  `postdate` datetime default NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `recipeid` (`recipeid`,`userid`),
  KEY `comments_users` (`userid`),
  KEY `comments_recipes` (`recipeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `comments_seq`
-- 

CREATE TABLE `comments_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `groceryitems`
-- 

CREATE TABLE `groceryitems` (
  `id` mediumint(8) unsigned NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL,
  `description` varchar(100) NOT NULL,
  `orderid` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `groceryitems_seq`
-- 

CREATE TABLE `groceryitems_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `images`
-- 

CREATE TABLE `images` (
  `id` mediumint(8) unsigned NOT NULL,
  `uid` char(30) NOT NULL,
  `recipeid` mediumint(8) unsigned NOT NULL,
  `recipeuid` char(30) NOT NULL,
  `caption` varchar(200) NOT NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `type` char(4) NOT NULL,
  `submittedby` mediumint(8) unsigned NOT NULL,
  `createdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `images_submitter` (`submittedby`),
  KEY `images_recipe` (`recipeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `images_seq`
-- 

CREATE TABLE `images_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `ingredients`
-- 

CREATE TABLE `ingredients` (
  `id` mediumint(8) unsigned NOT NULL,
  `setid` mediumint(8) unsigned NOT NULL,
  `amount` char(15) NOT NULL,
  `description` varchar(50) NOT NULL,
  `orderid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `setid` (`setid`,`orderid`),
  KEY `ingredient_set` (`setid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `ingredients_seq`
-- 

CREATE TABLE `ingredients_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=466 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `ingredientsets`
-- 

CREATE TABLE `ingredientsets` (
  `id` mediumint(8) unsigned NOT NULL,
  `recipeid` mediumint(8) unsigned NOT NULL,
  `orderid` smallint(5) unsigned NOT NULL,
  `name` varchar(75) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `set_recipe` (`recipeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `ingredientsets_seq`
-- 

CREATE TABLE `ingredientsets_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `invitations`
-- 

CREATE TABLE `invitations` (
  `invitee` mediumint(8) unsigned NOT NULL,
  `inviter` mediumint(8) unsigned NOT NULL,
  `code` varchar(100) NOT NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `acceptdate` datetime default NULL,
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `invitee` (`invitee`,`inviter`),
  KEY `code_index` (`code`),
  KEY `invitee_user` (`invitee`),
  KEY `inviter_user` (`inviter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `mine`
-- 

CREATE TABLE `mine` (
  `userid` mediumint(8) unsigned NOT NULL,
  `recipeid` mediumint(8) unsigned NOT NULL,
  `createdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  UNIQUE KEY `userid` (`userid`,`recipeid`),
  KEY `mine_user` (`userid`),
  KEY `mine_recipe` (`recipeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `recipes`
-- 

CREATE TABLE `recipes` (
  `id` mediumint(8) unsigned NOT NULL,
  `name` char(60) NOT NULL,
  `uniqueid` char(30) NOT NULL,
  `source` char(100) default NULL,
  `preheat` varchar(6) default NULL,
  `submittedby` mediumint(8) unsigned NOT NULL,
  `note` blob,
  `description` blob,
  `serves` smallint(6) default NULL,
  `preptime` smallint(6) default NULL,
  `cooktime` smallint(6) default NULL,
  `cached_rating` smallint(6) default NULL,
  `cached_ratinghits` smallint(6) default NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `recipes_users` (`submittedby`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `recipes_seq`
-- 

CREATE TABLE `recipes_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `recipetocategory`
-- 

CREATE TABLE `recipetocategory` (
  `recipeid` mediumint(8) unsigned NOT NULL,
  `categoryid` smallint(5) unsigned NOT NULL,
  UNIQUE KEY `recipeid` (`recipeid`,`categoryid`),
  KEY `rc_recipes` (`recipeid`),
  KEY `rc_categories` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `steps`
-- 

CREATE TABLE `steps` (
  `id` mediumint(8) unsigned NOT NULL,
  `recipeid` mediumint(8) unsigned NOT NULL,
  `orderid` smallint(5) unsigned NOT NULL,
  `step` blob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `step_recipe` (`recipeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `steps_seq`
-- 

CREATE TABLE `steps_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` char(33) NOT NULL,
  `auth` char(32) default NULL,
  `disabled` tinyint(4) default NULL,
  `invited` tinyint(4) default NULL,
  `readonly` smallint(5) unsigned NOT NULL,
  `admin` smallint(5) unsigned NOT NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` VALUES (2, 'root@root', 'Root User', 'password', '', 0, 0, 0, 1, '2007-06-12 20:10:35', '2007-03-24 13:36:08'); 


-- --------------------------------------------------------

-- 
-- Table structure for table `users_seq`
-- 

CREATE TABLE `users_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `comments`
-- 
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `groceryitems`
-- 
ALTER TABLE `groceryitems`
  ADD CONSTRAINT `groceryitems_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `images`
-- 
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `images_ibfk_2` FOREIGN KEY (`submittedby`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `ingredients`
-- 
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`setid`) REFERENCES `ingredientsets` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `ingredientsets`
-- 
ALTER TABLE `ingredientsets`
  ADD CONSTRAINT `ingredientsets_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `invitations`
-- 
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`invitee`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`inviter`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `mine`
-- 
ALTER TABLE `mine`
  ADD CONSTRAINT `mine_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mine_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `recipes`
-- 
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`submittedby`) REFERENCES `users` (`id`);

-- 
-- Constraints for table `recipetocategory`
-- 
ALTER TABLE `recipetocategory`
  ADD CONSTRAINT `recipetocategory_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipetocategory_ibfk_2` FOREIGN KEY (`categoryid`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `steps`
-- 
ALTER TABLE `steps`
  ADD CONSTRAINT `steps_ibfk_1` FOREIGN KEY (`recipeid`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
