<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Initial Schema
 *
 * @package    OAuth2
 * @category   Migration
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Migration_Oauth2_20110822174823 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `oauth2_access_tokens` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` int(10) unsigned NOT NULL,
  `scope` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `oauth2_auth_codes` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `scope` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `oauth2_clients` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) DEFAULT NULL,
  `client_id` varchar(20) NOT NULL,
  `client_secret` varchar(20) NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');

		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `oauth2_refresh_tokens` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` int(10) unsigned NOT NULL,
  `scope` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_access_tokens`;');
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_auth_codes`;');
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_clients`;');
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_refresh_tokens`;');
	}
}
