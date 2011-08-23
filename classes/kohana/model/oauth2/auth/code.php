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
class Kohana_Model_OAuth2_Auth_Code extends Model_OAuth2 {
	/**
	 * @var  string  Table name
	 */
	protected $_table_name = 'oauth2_auth_codes';

	/**
	 * @var  integer  Lifetime in seconds
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
	 * @param  string  $code
	 * @return Model_OAuth2_Auth_Code
	 */
	public static function find_code($code, $client_id = NULL)
	{
		$result = DB::select()
			->from(Model_OAuth2_Auth_Code::$_table_name)
			->where('code', '=', $code)
			->where('expires', '>=', time())
			->as_object('Model_OAuth2_Auth_Code', array(array('loaded' => TRUE, 'saved' => TRUE)))
			->execute();

		return ($result->count() > 0) ? $result->current() : new Model_OAuth2_Auth_Code();
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
		$code = new Model_OAuth2_Auth_Code;

		$code->code = UUID::v4();
		$code->expires = time() + Model_OAuth2_Auth_Code::$lifetime;
		$code->client_id = $client_id;
		$code->user_id = $user_id;
		$code->redirect_uri = $redirect_uri;
		$code->scope = $scope;

		return $code->save();
	}

	/**
	 * Deletes a auth code
	 */
	public static function delete_code($code)
	{
		return Model_OAuth2_Auth_Code::find_code($code)->delete();
	}
}