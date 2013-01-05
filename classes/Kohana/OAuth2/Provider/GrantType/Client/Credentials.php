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
class Kohana_OAuth2_Provider_GrantType_Client_Credentials extends OAuth2_Provider_GrantType {

	/**
	 * @var array Request Paramaters
	 */
	protected $_params = array(
		'scope',
	);

	public function validate_request()
	{
		// Get the request paramaters..
		$params = $this->_get_request_params();

		// Prepare validation
		$validation = Validation::factory($params);
//			->rule('scope', 'in_array', array(':value', $this->_config->scopes));

		$valid = $validation->check();

		if ( ! $valid)
			throw new OAuth2_Exception_InvalidRequest('Invalid Request .. '.json_encode($validation->errors()));

	}

	/**
	 * Get the user_id for the current request
	 *
	 * @return string
	 */
	public function get_user_id()
	{
		return NULL;
	}
}