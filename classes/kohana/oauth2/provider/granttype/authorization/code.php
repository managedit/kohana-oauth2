<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Provider_GrantType_Authorization_Code extends OAuth2_Provider_GrantType {

	/**
	 * @var array Request Paramaters
	 */
	protected $_params = array(
		'code',
		'redirect_uri',
	);

	public function validate_request()
	{
		// Get the request paramaters..
		$params = $this->_get_request_params();

		// Prepare validation
		$validation = Validation::factory($params)
			->rule('code',         'not_empty')
			->rule('code',         'uuid::valid')
			->rule('redirect_uri', 'not_empty')
			->rule('redirect_uri', 'url');

		$valid = $validation->check();

		if ( ! $valid)
			throw new OAuth2_Exception_InvalidRequest('Invalid Request .. '.json_encode($validation->errors()));

		// Lookup the auth code
		$auth_code = Model_OAuth2_Auth_Code::find_code($params['code']);

		// Is the auth code valid?
		if ( ! $auth_code->loaded())
			throw new OAuth2_Exception_InvalidGrant('The supplied code is unknown or invalid');

		// Was the auth code issued to this client?
		if ($auth_code->client_id != $this->_client->client_id)
			throw new OAuth2_Exception_InvalidGrant('The supplied code was issued to another client');

		// Does the redirect uri match what was supplied in the initial authorization request?
		if ($auth_code->redirect_uri !== $params['redirect_uri'])
			throw new OAuth2_Exception_InvalidGrant('The supplied redirect_uri does not match the initial authorization request\'s redirect_uri');
	}

	/**
	 * Get the user_id for the current request
	 *
	 * @return string
	 */
	public function get_user_id()
	{
		// Lookup the auth code
		$auth_code = Model_OAuth2_Auth_Code::find_code($this->_get_request_param('code'));

		return $auth_code->user_id;
	}

	/**
	 * Cleanup anything that may need expiring
	 */
	public function cleanup()
	{
		Model_OAuth2_Auth_Code::delete_code($this->_get_request_param('code'));
	}

}