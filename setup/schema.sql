BEGIN work;

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL auto_increment,
  `link` varchar(255) default NULL,
  `title` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `articles_link` (`link`)
) TYPE=MyISAM;

CREATE TABLE `blog` (
  `blog_id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL default '0',
  `title` text,
  `body` text,
  PRIMARY KEY  (`blog_id`)
) TYPE=MyISAM;

CREATE TABLE `blog_comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `blog_id` int(11) NOT NULL references blog on update cascade on delete cascade,
  `timestamp` int(11) NOT NULL default '0',
  `nickname` text,
  `email` text,
  `subject` text,
  `body` text,
  PRIMARY KEY  (`comment_id`)
) TYPE=MyISAM;

CREATE TABLE `music` (
  `music_id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `genre` text NOT NULL,
  `length` text NOT NULL,
  `filename` text NOT NULL,
  `timestamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`music_id`)
) TYPE=MyISAM;

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL auto_increment,
  `name` text,
  `link` varchar(255),
  `create_timestamp` int(11) default NULL,
  `update_timestamp` int(11) default NULL,
  `bin_package_url` text default NULL,
  `src_package_url` text default NULL,
  `description` text,
  `changelog` text,
  `version` text,
  PRIMARY KEY  (`project_id`),
  UNIQUE KEY `project_link` (`link`)
) TYPE=MyISAM;

CREATE TABLE `project_screenshots` (
  `screenshot_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) not null references `projects` on update cascade on delete cascade,
  `caption` text default NULL,
  PRIMARY KEY (`screenshot_id`)
) TYPE=MyISAM;

CREATE TABLE `quotes` (
  `quote_id` int(11) NOT NULL auto_increment,
  `quote` text,
  `author` text,
  PRIMARY KEY  (`quote_id`)
) TYPE=MyISAM;

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL auto_increment,
  `priority` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `cost` text NOT NULL,
  `foundat` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY  (`wishlist_id`)
) TYPE=MyISAM;

CREATE TABLE `config` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255),
  PRIMARY KEY (`key`)
) TYPE=MyISAM;

CREATE TABLE `active_modules` (
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) TYPE=MyISAM;

CREATE TABLE `navigation` (
  `nav_id` integer NOT NULL auto_increment,
  `type` integer NOT NULL default '0',
  `url` varchar(255),
  `caption` varchar(255),
  `module_id` varchar(255),
  `order_id` integer NOT NULL UNIQUE,
  PRIMARY KEY (`nav_id`)
) TYPE=MyISAM;

COMMIT;
