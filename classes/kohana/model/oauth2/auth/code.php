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
class Kohana_Model_OAuth2_Auth_Code
	extends Model_Database
	implements Model_OAuth2_Interface_Auth_Code,
		Kohana_Model_OAuth2_Interface_Oauth
{
	protected $_table = 'oauth2_auth_codes';

	/**
	 * @var integer Lifetime
	 */
	public static $lifetime = 30;

	/**
	 * Find a auth code
	 *
	 * @param string $code      code to find
	 * @param int    $client_id client id to pair with
	 * 
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function find_code($code, $client_id = NULL)
	{
		$query = db::select('*')->from('oauth2_auth_codes')
			->where('code', '=', $client_id)
			->where('expires', '>=', time());

		if (NULL !== $client_id)
		{
			$query->where('client_id', '=', $client_id);
		}

		$result = $query->as_object()->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Create a auth code
	 *
	 * @param int    $client_id    client id to create with
	 * @param string $redirect_uri redirect uri to create with
	 * @param int    $user_id      the user id to create with
	 * @param string $scope        scope to create with
	 * 
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function create_code(
		$client_id, $redirect_uri, $user_id = NULL, $scope = NULL
	)
	{
		$keys = array(
			'code', 'expires', 'client_id', 'user_id', 'redirect_uri', 'scope'
		);
		$vals = array(
			UUID::v4(),
			time() + Model_OAuth2_Access_Token::$lifetime,
			$client_id,
			$user_id,
			$redirect_uri,
			$scope
		);

		$token = db::insert('oauth2_auth_codes', $keys)
			->values($vals)
			->execute();

		return (object) array_combine($keys, $vals);
	}

	/**
	 * Deletes a auth code
	 * 
	 * @param string $code the code to delete
	 */
	public static function delete_code($code)
	{
		db::delete('oauth2_auth_codes')
			->where('code', '=', $code)
			->execute();
	}
}