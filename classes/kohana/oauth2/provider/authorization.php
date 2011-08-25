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
		// TODO: This shouldn't be hardcoded...

		$authorization_header = $request->headers('Authorization');
		
		if (preg_match('/^Bearer (.*)/i', $authorization_header))
		{
			$class = 'OAuth2_Provider_Authorization_Bearer';
		}
		elseif (preg_match('/^Basic (.*)/i', $authorization_header))
		{
			$class = 'OAuth2_Provider_Authorization_Basic';
		}
		else
		{
			$class = 'OAuth2_Provider_Authorization_Body';
		}

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