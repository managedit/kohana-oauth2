<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Switch to UUID's for client_id and client_secret
 *
 * @package    OAuth2
 * @category   Migration
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Migration_Oauth2_20110823104806 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE  `oauth2_clients` CHANGE  `client_id`  `client_id` VARCHAR( 37 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_clients` CHANGE  `client_secret`  `client_secret` VARCHAR( 37 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_access_tokens` CHANGE  `client_id`  `client_id` VARCHAR( 37 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_auth_codes` CHANGE  `client_id`  `client_id` VARCHAR( 37 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_refresh_tokens` CHANGE  `client_id`  `client_id` VARCHAR( 37 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE  `oauth2_clients` CHANGE  `client_id`  `client_id` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_clients` CHANGE  `client_secret`  `client_secret` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_access_tokens` CHANGE  `client_id`  `client_id` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_auth_codes` CHANGE  `client_id`  `client_id` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
		$db->query(NULL, 'ALTER TABLE  `oauth2_refresh_tokens` CHANGE  `client_id`  `client_id` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
	}
}
