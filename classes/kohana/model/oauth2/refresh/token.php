<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_Model_OAuth2_Refresh_Token extends ORM {

	/**
	 * @var  integer  Token Lifetime in seconds
	 */
	public static $lifetime = 15552000; // 6 Months

	/**
	 * Find a token
	 *
	 * @param  string  $refresh_token
	 * @return Model_OAuth2_Refresh_Token
	 */
	public static function find_token($refresh_token, $client_id = NULL)
	{
		$token = ORM::factory('oauth2_refresh_token')
			->where('refresh_token', '=', $refresh_token)
			->where('expires', '>=', time());

		if ($client_id !== NULL)
		{
			$token->where('client_id', '=', $client_id);
		}

		return $token->find();
	}

	/**
	 * Create a token
	 *
	 * @param  string  $client_id
	 * @param  string  $scope
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function create_token($client_id, $user_id = NULL, $scope = NULL)
	{
		$token = ORM::factory('oauth2_refresh_token')
			->values(array(
				'refresh_token' => UUID::v4(),
				'expires'       => time() + Model_OAuth2_Refresh_Token::$lifetime,
				'client_id'     => $client_id,
				'user_id'       => $user_id,
				'scope'         => $scope,
			))
			->save();

		return $token;
	}

	/**
	 * Deletes a token
	 */
	public static function delete_token($refresh_token)
	{
		Model_OAuth2_Refresh_Token::find_token($refresh_token)->delete();
	}
}