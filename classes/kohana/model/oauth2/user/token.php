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
class Kohana_Model_OAuth2_User_Token
	extends Model_OAuth2
	implements Interface_Model_OAuth2_User_Token
{
	protected $_table_name = 'oauth2_user_tokens';

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array(
		'id',
		'provider',
		'token_type',
		'access_token',
		'refresh_token',
		'user_id',
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
	public static function find_token($provider, $user_id = NULL)
	{
		$query = db::select('*')->from('oauth2_user_tokens')
			->where('provider', '=', $provider)
			->order_by('id', 'DESC');

		if (NULL !== $user_id)
		{
			$query->where('user_id', '=', $user_id);
		}

		$result = $query->as_object('Model_OAuth2_User_Token', array(
			array('loaded' => TRUE, 'saved' => TRUE)
		))->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return new Model_OAuth2_User_Token;
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
	public static function create_token($provider, $token_type, $access_token, $user_id = NULL, $refresh_token = NULL)
	{
		$token = new Model_OAuth2_User_Token(
			array(
				'data' => array(
					'provider' => $provider,
					'token_type' => $token_type,
					'access_token' => $access_token,
					'refresh_token' => $refresh_token,
					'user_id' => $user_id,
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
	public static function delete_token($provider, $user_id = NULL)
	{
		return Model_OAuth2_User_Token::find_token($provider, $user_id)->delete();
	}
}