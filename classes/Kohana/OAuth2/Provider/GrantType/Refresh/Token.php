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
class Kohana_OAuth2_Provider_GrantType_Refresh_Token extends OAuth2_Provider_GrantType {

	/**
	 * @var array Request Paramaters
	 */
	protected $_params = array(
		'refresh_token',
		'scope',
	);

	public function validate_request()
	{
		// Get the request paramaters..
		$params = $this->_get_request_params();

		// Prepare validation
		$validation = Validation::factory($params)
			->rule('refresh_token', 'not_empty')
			->rule('refresh_token', 'uuid::valid');
//			->rule('scope', 'in_array', array(':value', $this->_config->scopes));

		$valid = $validation->check();

		if ( ! $valid)
			throw new OAuth2_Exception_InvalidRequest('Invalid Request .. '.json_encode($validation->errors()));

		// Lookup the token
		$token = Model_OAuth2_Refresh_Token::find_token($params['refresh_token']);

		// Is the token valid?
		if ( ! $token->loaded())
			throw new OAuth2_Exception_InvalidGrant('The supplied refresh_token is unknown or invalid');

		// Was the auth code issued to this client?
		if ($token->client_id != $this->_client->client_id)
			throw new OAuth2_Exception_InvalidGrant('The supplied refresh_token was issued to another client');
	}

	/**
	 * Get the user_id for the current request
	 *
	 * @return string
	 */
	public function get_user_id()
	{
		// Lookup the token
		$token = Model_OAuth2_Refresh_Token::find_token($this->_get_request_param('refresh_token'));

		return $token->user_id;
	}

	/**
	 * Cleanup anything that may need expiring
	 */
	public function cleanup()
	{
		Model_OAuth2_Refresh_Token::delete_token($this->_get_request_param('refresh_token'));
	}

}