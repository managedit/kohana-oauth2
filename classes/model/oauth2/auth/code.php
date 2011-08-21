<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Model_OAuth2_Auth_Code extends ORM {

	/**
	 * @var  integer  Lifetime
	 */
	public static $lifetime = 30;

	/**
	 * Find a auth code
	 *
	 * @param  string  $code
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function find_code($code, $client_id = NULL)
	{
		$code = ORM::factory('oauth2_auth_code')
			->where('code', '=', $code)
			->find();

		return $code;
	}

	/**
	 * Validate an auth code
	 *
	 * @param  string  $code
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function validate_code($code, $client_id = NULL)
	{
		$code = Model_OAuth2_Auth_Code::find_code($code, $client_id);

		if ( ! $code->loaded())
			return FALSE;

		if ($code->expires <= time())
			return FALSE;

		return TRUE;
	}

	/**
	 * Create a auth code
	 *
	 * @param  string  $client_id
	 * @param  string  $redirect_uri
	 * @param  string  $scope
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function create_code($client_id, $redirect_uri, $user_id = NULL, $scope = NULL)
	{
		$code = ORM::factory('oauth2_auth_code')
			->values(array(
				'code'         => UUID::v4(),
				'expires'      => time() + Model_OAuth2_Auth_Code::$lifetime,
				'client_id'    => $client_id,
				'user_id'      => $user_id,
				'redirect_uri' => $redirect_uri,
				'scope'        => $scope,
			))
			->save();

		return $code;
	}

	/**
	 * Deletes a auth code
	 */
	public static function delete_code($code)
	{
		Model_OAuth2_Auth_Code::find_code($code)->delete();
	}
}