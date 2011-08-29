<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Body Authorization
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Provider_Authorization_Body extends OAuth2_Provider_Authorization {

	/**
	 * Gets the client_id.
	 *
	 * @return string
	 */
	public function get_client_id()
	{
		if ($this->_request->method() == Request::GET)
		{
			// This is a breach of the spec .. GET isnt really allowed use this method!
			return $this->_request->query('client_id');
		}
		else if ($this->_request->method() == Request::POST)
		{
			return $this->_request->post('client_id');
		}
		else
		{
			// TODO: Raise something here?
		}
	}

	/**
	 * Gets the client_secret.
	 *
	 * @return string
	 */
	public function get_client_secret()
	{
		if ($this->_request->method() == Request::GET)
		{
			// This is a breach of the spec .. GET isnt really allowed use this method!
			return $this->_request->query('client_secret');
		}
		else if ($this->_request->method() == Request::POST)
		{
			return $this->_request->post('client_secret');
		}
		else
		{
			// TODO: Raise something here?
		}
	}

}