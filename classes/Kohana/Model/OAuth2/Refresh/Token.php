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
	extends Model_OAuth2
	implements Interface_Model_OAuth2_Refresh_Token
{
	protected $_table_name = 'oauth2_refresh_tokens';

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array(
		'id', 'refresh_token', 'expires', 'client_id', 'user_id', 'scope'
	);

	/**
	 * @var  integer  Token Lifetime in seconds
	 */
	public static $lifetime = 15552000; // 6 Months

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
			->where('refresh_token', '=', $refresh_token)
			->where('expires', '>=', time());

		if (NULL !== $client_id)
		{
			$query->where('client_id', '=', $client_id);
		}

		$result = $query->as_object('Model_OAuth2_Refresh_Token', array(
			array('loaded' => TRUE, 'saved' => TRUE)
		))->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return new Model_OAuth2_Refresh_Token;
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
		$token = new Model_OAuth2_Refresh_Token(
			array(
				'data' => array(
					'refresh_token' => UUID::v4(),
					'expires' => time() + Model_OAuth2_Refresh_Token::$lifetime,
					'client_id' => $client_id,
					'user_id' => $user_id,
					'scope' => serialize($scope),
				)
			)
		);

		$token->save();

		return $token;
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
		Model_OAuth2_Refresh_Token::find_token($refresh_token)->delete();
	}

	/**
	 * Deletes expired tokens
	 *
	 * @return integer Number of tokens deleted
	 */
	public static function deleted_expired_tokens()
	{

		$rows_deleted = DB::delete('oauth2_refresh_tokens')
			->where('expires', '<=', time())
			->execute();

		return $rows_deleted;
	}
}