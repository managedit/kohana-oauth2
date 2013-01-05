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
	extends Model_OAuth2
	implements Interface_Model_OAuth2_Auth_Code
{
	protected $_table_name = 'oauth2_auth_codes';

	/**
	 * @var  integer  Lifetime
	 */
	public static $lifetime = 30;

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array(
		'id',
		'code',
		'client_id',
		'user_id',
		'redirect_uri',
		'expires',
		'scope'
	);

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
		$result = DB::select()
			->from('oauth2_auth_codes')
			->where('code', '=', $code)
			->where('expires', '>=', time())
			->as_object('Model_OAuth2_Auth_Code', array(
				array('loaded' => TRUE, 'saved' => TRUE)
			))
			->execute();

		return ($result->count() > 0)
			? $result->current()
			: new Model_OAuth2_Auth_Code;
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
		$code = new Model_OAuth2_Auth_Code(
			array(
				'data' => array(
					'code' => UUID::v4(),
					'expires' => time() + Model_OAuth2_Auth_Code::$lifetime,
					'client_id' => $client_id,
					'user_id' => $user_id,
					'redirect_uri' => $redirect_uri,
					'scope' => serialize($scope)
				)
			)
		);

		$code->save();

		return $code;
	}

	/**
	 * Deletes a auth code
	 *
	 * @param string $code the code to delete
	 */
	public static function delete_code($code)
	{
		return Model_OAuth2_Auth_Code::find_code($code)->delete();
	}

	/**
	 * Deletes expired codes
	 *
	 * @return  integer  Number of codes deleted
	 */
	public static function deleted_expired_codes()
	{
		$rows_deleted = DB::delete('oauth2_auth_codes')
			->where('expires', '<=', time())
			->execute();

		return $rows_deleted;
	}
}
