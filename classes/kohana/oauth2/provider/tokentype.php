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
abstract class Kohana_OAuth2_Provider_TokenType {

	public static function factory($request)
	{
		if (preg_match('/^([A-Za-z]+) .*/i', $request->headers('Authorization'), $matches))
		{
			$class = 'OAuth2_Provider_TokenType_'.$matches[1];
		}
		/**
		 * There are some PITA sections of the spec to check for..
		 *
		 * @link http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-08#section-2.2
		 * @link http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-08#section-2.3
		 */
		else if ($request->post('access_token') !== NULL)
		{

			$class = 'OAuth2_Provider_TokenType_Bearer';
		}
		else if ($request->query('access_token') !== NULL)
		{
			$class = 'OAuth2_Provider_TokenType_Bearer';
		}
		else
		{
			throw new OAuth2_Exception_InvalidRequest('Invalid or unknown token type');
		}

		if ( ! class_exists($class))
			throw new OAuth2_Exception_InvalidRequest('Invalid or unknown token type');

		return new $class($request);
	}

	/**
	 * @var Request
	 */
	protected $_request;

	/**
	 *
	 * @param type $request
	 */
	public function __construct($request)
	{
		$this->_request = $request;

		$this->validate();
	}

	/**
	 * Validates the request
	 */
	abstract protected function validate();

	/**
	 * Get the name for this token type
	 *
	 * @return string
	 */
	abstract public function get_token_type();


	/**
	 * Get the additional params for this token type
	 *
	 * @return array
	 */
	abstract public function get_token_params();

	/**
	 * Get the additional headers for this token type
	 *
	 * @return array
	 */
	abstract public function get_token_headers();

	/**
	 * Gets the current client
	 *
	 * @return Model_OAuth2_Client
	 */
	abstract public function get_client();

	/**
	 * Gets the request user_id
	 *
	 * @return string
	 */
	abstract public function get_user_id();

}