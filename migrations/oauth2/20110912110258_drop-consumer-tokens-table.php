<?php defined('SYSPATH') or die('No direct script access.');/**
 * Drop Consumer Tokens Table
 */
class Migration_Oauth2_20110912110258 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_user_tokens`');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'CREATE TABLE IF NOT EXISTS `oauth2_user_tokens` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL,
  `token_type` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`refresh_token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;');
	}
}
