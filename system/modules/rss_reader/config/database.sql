-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `rss_feed` varchar(255) NOT NULL default '',
  `rss_template` varchar(32) NOT NULL default '',
  `rss_numberOfItems` smallint(5) unsigned NOT NULL default '0',
  `rss_cache` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
