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
class Kohana_Model_OAuth2_Refresh_Token
	extends Model_Database
	implements Model_OAuth2_Interface_Refresh_Token
{
	protected $_table = 'oauth2_refresh_tokens';

	/**
	 * @var  integer  Token Lifetime
	 */
	public static $lifetime = 2592000; // 30 Days

	/**
	 * Find a token
	 *
	 * @param string $refresh_token the token to find
	 * @param int    $client_id     the optional client id to find with
	 * 
	 * @return stdClass | null
	 */
	public static function find_token($refresh_token, $client_id = NULL)
	{
		$query = db::select('*')->from('oauth2_refresh_tokens')
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
	 * Create a token
	 *
	 * @param int    $client_id the client id to create with
	 * @param int    $user_id   the user id to create with
	 * @param string $scope     the scope to create with
	 * 
	 * @return stdClass
	 */
	public static function create_token(
		$client_id, $user_id = NULL, $scope = NULL
	)
	{
		$keys = array(
			'refresh_token', 'expires', 'client_id', 'user_id', 'scope'
		);
		$vals = array(
			UUID::v4(),
			time() + Model_OAuth2_Access_Token::$lifetime,
			$client_id,
			$user_id,
			$scope
		);

		$token = db::insert('oauth2_refresh_tokens', $keys)
			->values($vals)
			->execute();

		return (object) array_combine($keys, $vals);
	}

	/**
	 * Deletes a token
	 * 
	 * @param string $refresh_token the token to delete
	 * 
	 * @return null
	 */
	public static function delete_token($refresh_token)
	{
		db::delete('oauth2_refresh_tokens')
			->where('refresh_token', '=', $refresh_token)
			->execute();
	}
}