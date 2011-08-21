<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Model_OAuth2_Access_Token extends ORM {

	/**
	 * @var  integer  Token Lifetime
	 */
	public static $lifetime = 3600;

	/**
	 * Find a token
	 *
	 * @param  string  $oauth_token
	 * @return Model_OAuth2_Token
	 */
	public static function find_token($access_token, $client_id = NULL)
	{
		$token = ORM::factory('oauth2_access_token')
			->where('access_token', '=', $access_token);

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
		$token = ORM::factory('oauth2_access_token')
			->values(array(
				'access_token' => UUID::v4(),
				'expires'      => time() + Model_OAuth2_Access_Token::$lifetime,
				'client_id'    => $client_id,
				'user_id'      => $user_id,
				'scope'        => $scope,
			))
			->save();

		return $token;
	}

	/**
	 * Deletes a token
	 */
	public static function delete_token($access_token)
	{
		Model_OAuth2_Access_Token::find_token($access_token)->delete();
	}
}