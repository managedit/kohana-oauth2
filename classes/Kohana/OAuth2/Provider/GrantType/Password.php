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
abstract class Kohana_OAuth2_Provider_GrantType_Password extends OAuth2_Provider_GrantType {

	/**
	 * @var array Request Paramaters
	 */
	protected $_params = array(
		'username',
		'password',
		'scope',
	);

	public function validate_request()
	{
		// Get the request paramaters..
		$params = $this->_get_request_params();

		// Prepare validation
		$validation = Validation::factory($params)
			->rule('username', 'not_empty')
			->rule('password', 'not_empty');
//			->rule('scope',    'in_array', array(':value', $this->_config->scopes));

		$valid = $validation->check();

		if ( ! $valid)
			throw new OAuth2_Exception_InvalidRequest('Invalid Request .. '.json_encode($validation->errors()));

		// Validate the username and password
		if ( ! $this->_validate_user($params['username'], $params['password']))
			throw new OAuth2_Exception_InvalidGrant('Invalid username or password');

	}

	/**
	 * Validates a username or password.
	 *
	 * This method must be implemented per application!
	 *
	 * @param  string $username Username
	 * @param  string $password Password
	 *
	 * @return string User ID
	 */
	abstract protected function _validate_user($username, $password);
}