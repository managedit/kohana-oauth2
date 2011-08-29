<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Bearer token support
 *
 * @link       http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
abstract class Kohana_OAuth2_Provider_TokenType_Bearer extends OAuth2_Provider_TokenType {

	const TOKEN_TYPE = 'Bearer';

	/**
	 * @var Model_OAuth2_Client
	 */
	protected $_client;

	/**
	 * Get the name for this token type
	 *
	 * @return string
	 */
	public function get_token_type()
	{
		return OAuth2_Provider_TokenType_Bearer::TOKEN_TYPE;
	}

	/**
	 * Validates the request
	 */
	protected function validate()
	{
		$access_token = Model_OAuth2_Access_Token::find_token($this->_find_token_string());

		if ( ! $access_token->loaded())
			throw new OAuth2_Exception_InvalidToken('The access token provided is expired, revoked, malformed, or invalid for other reasons.');

		$client = Model_OAuth2_Client::find_client($access_token->client_id);

		if ( ! $client->loaded())
			throw new OAuth2_Exception_InvalidToken('The access token provided is expired, revoked, malformed, or invalid for other reasons.');

		$this->_client = $client;
	}

	protected function _find_token_string()
	{
		$authorization_header = $this->_request->headers('Authorization');

		$header = preg_match('/^Bearer (.*)/i', $authorization_header, $matches);

		if ($header)
		{
			return $matches[1];
		}
		/**
		 * There are some PITA sections of the spec to check for..
		 *
		 * @link http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-08#section-2.2
		 * @link http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-08#section-2.3
		 */
		else if ($this->_request->post('access_token') !== NULL)
		{
			return  $this->_request->post('access_token');
		}
		else if ($this->_request->query('access_token') !== NULL)
		{
			return $this->_request->query('access_token');
		}
		else
		{
			throw new OAuth2_Exception_InvalidToken('The access token provided is expired, revoked, malformed, or invalid for other reasons.');
		}
	}

	/**
	 * Get the additional params for this token type
	 *
	 * @return array
	 */
	public function get_token_params()
	{
		return array(
			'token_type' => OAuth2_Provider_TokenType_Bearer::TOKEN_TYPE,
		);
	}

	/**
	 * Get the additional headers for this token type
	 *
	 * @return array
	 */
	public function get_token_headers()
	{
		return array();
	}

	/**
	 * Gets the current client
	 *
	 * @return Model_OAuth2_Client
	 */
	public function get_client()
	{
		return $this->_client;
	}

	/**
	 * Gets the request user_id
	 *
	 * @return string
	 */
	public function get_user_id()
	{
		$access_token = Model_OAuth2_Access_Token::find_token($this->_find_token_string());

		if ( ! $access_token->loaded())
			throw new OAuth2_Exception_InvalidClient('Client authentication failed');

		return $access_token->user_id;
	}
}