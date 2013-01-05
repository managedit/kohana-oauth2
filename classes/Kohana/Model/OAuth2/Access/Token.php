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
	extends Model_OAuth2
	implements Interface_Model_OAuth2_Access_Token
{
	protected $_table_name = 'oauth2_access_tokens';

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array(
		'id', 'access_token', 'expires', 'client_id', 'user_id', 'scope'
	);

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
			->where('access_token', '=', $access_token)
			->where('expires', '>=', time());

		if (NULL !== $client_id)
		{
			$query->where('client_id', '=', $client_id);
		}

		$result = $query->as_object('Model_OAuth2_Access_Token', array(
			array('loaded' => TRUE, 'saved' => TRUE)
		))->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return new Model_OAuth2_Access_Token;
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
	public static function create_token(
		$client_id, $user_id = NULL, $scope = NULL
	)
	{
		$token = new Model_OAuth2_Access_Token(
			array(
				'data' => array(
					'access_token' => UUID::v4(),
					'expires' => time() + Model_OAuth2_Access_Token::$lifetime,
					'client_id' => $client_id,
					'user_id' => $user_id,
					'scope' => serialize($scope)
				)
			)
		);

		$token->save();

		return $token;
	}

	/**
	 * Deletes an access token
	 *
	 * @param string $access_token the token to delete
	 *
	 * @return null
	 */
	public static function delete_token($access_token)
	{
		return Model_OAuth2_Access_Token::find_token($access_token)->delete();
	}

	/**
	 * Deletes expired tokens
	 *
	 * @return integer Number of tokens deleted
	 */
	public static function deleted_expired_tokens()
	{

		$rows_deleted = DB::delete('oauth2_access_tokens')
			->where('expires', '<=', time())
			->execute();

		return $rows_deleted;
	}
}