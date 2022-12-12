CREATE TABLE `tl_ls_data_collector` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `alias` varchar(128) BINARY NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `formId` int(10) unsigned NOT NULL default '0'
  PRIMARY KEY  (`id`),
) ENGINE=InnoDB DEFAULT;
