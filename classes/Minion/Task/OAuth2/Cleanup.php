<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Purges expired access_tokens, refresh_tokens, and authorization_code's.
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Minion_Task_OAuth2_Cleanup extends Minion_Task
{
	/**
	 * Clears the cache
	 */
	public function execute(array $config)
	{
		$access_tokens = Model_OAuth2_Access_Token::deleted_expired_tokens();
		$refresh_tokens = Model_OAuth2_Refresh_Token::deleted_expired_tokens();
		$codes = Model_OAuth2_Auth_Code::deleted_expired_codes();

		Minion_CLI::write('Deleted '.$access_tokens.' access tokens');
		Minion_CLI::write('Deleted '.$refresh_tokens.' refresh tokens');
		Minion_CLI::write('Deleted '.$codes.' auth codes');

		Minion_CLI::write('Cleanup complete');
	}
}
