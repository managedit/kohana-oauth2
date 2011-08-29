<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Mac token support.
 *
 * Work in progress! Basically a class skel, nothing more.
 *
 * @link       http://tools.ietf.org/html/draft-ietf-oauth-v2-http-mac
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Provider_TokenType_Mac extends OAuth2_Provider_TokenType {

	const TOKEN_TYPE = 'mac';

	/**
	 * Get the name for this token type
	 *
	 * @return string
	 */
	public function get_token_type()
	{
		return OAuth2_Provider_TokenType_Mac::TOKEN_TYPE;
	}

	/**
	 * Get the additional params for this token type
	 *
	 * @return array
	 */
	public function get_token_params()
	{
		return array(
			'token_type'    => OAuth2_Provider_TokenType_Mac::TOKEN_TYPE,
			'mac_key'       => 'abc',
			'mac_algorithm' => 'hmac-sha-256',
		);
	}

	/**
	 * Get the additional headers for this token type
	 *
	 * @return array
	 */
	public function get_token_headers()
	{
		return array(
			'Set-Cookie' => 'SID=31d4d96e407aad42; Path=/; Domain=example.com; MAC-Key=8yfrufh348h; MAC-Algorithm=hmac-sha-1'
		);
	}

	/**
	 * Gets the current client
	 *
	 * @return Model_OAuth2_Client
	 */
	public function get_client()
	{
		throw new OAuth2_Exception_InvalidClient('The \'mac\' token type has not been implemented');
	}

	/**
	 * Gets the request user_id
	 *
	 * @return string
	 */
	public function get_user_id()
	{
		throw new OAuth2_Exception_InvalidClient('The \'mac\' token type has not been implemented');
	}
}