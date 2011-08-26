<?php defined('SYSPATH') or die('No direct script access.');

/**
 * HTTP Bearer Authorization
 *
 * Probably doesnt handle failures like it should yet ;)
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Provider_Authorization_Bearer extends OAuth2_Provider_Authorization {

	/**
	 * Gets the client_id.
	 *
	 * @return string
	 */
	public function get_client_id()
	{
		return $this->get_client()->client_id;
	}

	/**
	 * Gets the client_secret.
	 *
	 * @return string
	 */
	public function get_client_secret()
	{
		return $this->get_client()->client_secret;
	}

	/**
	 * Gets the current client
	 *
	 * @return Model_OAuth2_Client
	 */
	public function get_client()
	{
		$authorization_header = $this->_request->headers('Authorization');

		preg_match('/^Bearer (.*)/i', $authorization_header, $matches);

		$access_token = Model_OAuth2_Access_Token::find_token($matches[1]);

		return Model_OAuth2_Client::find_client($access_token->client_id);
	}

}