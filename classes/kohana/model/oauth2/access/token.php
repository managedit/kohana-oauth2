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
	implements Model_OAuth2_Interface_Access_Token
{
	$this->_table = 'oauth2_access_tokens';

	/**
	 * @var  integer  Token Lifetime
	 */
	public static $lifetime = 3600;

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
		$query = db::select('*')->from($this->_table)
			->where('access_token', '=', $client_id)
			->where('expires', '>=', time());

		if (NULL !== $client_id)
		{
			$query->where('client_id', '=', $client_id);
		}

		$result = $query->as_object()->execute($this->_db);

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

		$token = db::insert($this->_table, $keys)
			->values($vals)
			->execute($this->_db);

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
		db::delete($this->_table)
			->where('access_token', '=', $access_token)
			->execute($this->_db);
	}
}