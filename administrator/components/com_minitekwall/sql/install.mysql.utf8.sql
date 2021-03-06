CREATE TABLE IF NOT EXISTS `#__minitek_wall_widgets` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
 `type_id` varchar(100) NOT NULL,
 `source_id` varchar(100) NOT NULL,
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `masonry_params` text NOT NULL,
 `slider_params` text NOT NULL,
 `scroller_params` text NOT NULL,
 `state` tinyint(1) NOT NULL DEFAULT '0',
 `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__minitek_wall_widgets_source` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `widget_id` int(10) unsigned NOT NULL,
 `joomla_source` text NOT NULL,
 `k2_source` text NOT NULL,
 `virtuemart_source` text NOT NULL,
 `jomsocial_source` text NOT NULL,
 `easyblog_source` text NOT NULL,
 `folder_source` text NOT NULL,
 `rss_source` text NOT NULL,
 `easysocial_source` text NOT NULL,
 `custom_source` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__minitek_source_groups` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
 `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `state` tinyint(3) NOT NULL DEFAULT '0',
 `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__minitek_source_items` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
 `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
 `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `state` tinyint(3) NOT NULL DEFAULT '0',
 `groupid` int(10) unsigned NOT NULL DEFAULT '0',
 `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `created_by` int(10) unsigned NOT NULL DEFAULT '0',
 `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `images` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `urls` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `tags` text COLLATE utf8mb4_unicode_ci NOT NULL,
 `ordering` int(11) NOT NULL DEFAULT '0',
 `access` int(10) unsigned NOT NULL DEFAULT '0',
 `featured` tinyint(3) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__minitek_wall_grids` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
 `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `columns` tinyint(3) unsigned NOT NULL,
 `state` tinyint(3) NOT NULL DEFAULT '0',
 `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
 `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `elements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
