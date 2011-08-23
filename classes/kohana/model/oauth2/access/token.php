<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model to handle oauth2 access tokens
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_Model_OAuth2_Access_Token
	extends Model_Database
	implements Model_OAuth2_Interface_Access_Token,
		Kohana_Model_OAuth2_Interface_Oauth
{
	$this->_table = 'oauth2_access_tokens';

	/**
	 * @var  integer  Token Lifetime in seconds
	 */
	public static $lifetime = 900; // 5 Minutes

	/**
	 * Find an access token
	 *
	 * @param string $access_token token to find
	 * @param int    $client_id    client to match with
	 * 
	 * @return stdClass
	 */
	public static function find_token($access_token, $client_id = NULL)
	{
		$query = db::select('*')->from('oauth2_access_tokens')
			->where('access_token', '=', $client_id)
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
	 * Create an access token
	 *
	 * @param int $client_id client id to create with
	 * @param int $user_id   user id to create with
	 * @param int $scope     scope to create with
	 * 
	 * @return stdClass
	 */
	public static function create_token($client_id, $user_id = NULL, $scope = NULL)
	{
		$keys = array(
			'access_token', 'expires', 'client_id', 'user_id', 'scope'
		);
		$vals = array(
			UUID::v4(),
			time() + Model_OAuth2_Access_Token::$lifetime,
			$client_id,
			$user_id,
			$scope
		);

		$token = db::insert('oauth2_access_tokens', $keys)
			->values($vals)
			->execute();

		return (object) array_combine($keys, $vals);
	}

	/**
	 * Deletes an access token
	 * 
	 * @param string $access_token
	 * 
	 * @return null
	 */
	public static function delete_token($access_token)
	{
		db::delete('oauth2_access_tokens')
			->where('access_token', '=', $access_token)
			->execute();
	}
}