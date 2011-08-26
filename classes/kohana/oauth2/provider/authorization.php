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
abstract class Kohana_OAuth2_Provider_Authorization {

	public static function factory($request)
	{
		if (preg_match('/^([A-Za-z]+) .*/i', $request->headers('Authorization'), $matches))
		{
			$class = 'OAuth2_Provider_Authorization_'.$matches[1];
		}
		else
		{
			$class = 'OAuth2_Provider_Authorization_Body';
		}

		if ( ! class_exists($class))
			throw new OAuth2_Exception_InvalidClient('Client authentication failed');

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
	}

	/**
	 * Gets the client making this request
	 *
	 * @return Model_OAuth2_Client
	 */
	public function get_client()
	{
		return Model_OAuth2_Client::find_client($this->get_client_id(), $this->get_client_secret());
	}
}