<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Tokens
 *
 * @package    OAuth2
 * @category   Migration
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Migration_Oauth2_20110823104807 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
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

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE IF EXISTS `oauth2_user_tokens`');
	}
}
