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
abstract class Kohana_OAuth2_Provider_GrantType {

	/**
	 * @var array Request Paramaters
	 */
	protected $_params = array();

	public static function factory($request, $client)
	{
		$grant_type = $request->post('grant_type');

		if ($grant_type == '')
			throw new OAuth2_Exception_UnsupportedGrantType('Invalid or unknown grant type');

		$class = 'OAuth2_Provider_GrantType_'.$grant_type;

		if ( ! class_exists($class))
			throw new OAuth2_Exception_UnsupportedGrantType('Unknown or invalid grant_type');

		return new $class($request, $client);
	}

	/**
	 * @var Request
	 */
	protected $_request;

	/**
	 * @var Model_OAuth2_Client
	 */
	protected $_client;

	/**
	 *
	 * @param type $request
	 */
	public function __construct($request, $client)
	{
		$this->_request = $request;
		$this->_client = $client;
	}

	/**
	 *
	 * @return array Array of request paramaters
	 */
	protected function _get_request_params()
	{
		switch ($this->_request->method())
		{
			case Request::GET:
				$params = $this->_request->query();
				break;
			case Request::POST:
				$params =  $this->_request->post();
				break;
			default:
				throw new Exception('TODO: Make a proper exception for this! Invalid or unknown request method.');
		}

		return Arr::extract($params, $this->_params);
	}

	protected function _get_request_param($key)
	{
		$result = $this->_get_request_params();

		return (isset($result[$key])) ? $result[$key] : NULL;
	}

	/**
	 * Validate the request is valid for this grant type
	 */
	abstract public function validate_request();

	/**
	 * Get the user_id for the current request
	 *
	 * @return string
	 */
	abstract public function get_user_id();

	/**
	 * Get the scope(s) for the current request
	 *
	 * @return array
	 */
	public function get_scopes()
	{
		return array();
	}

	/**
	 * Cleanup anything that may need expiring
	 */
	public function cleanup()
	{

	}

}