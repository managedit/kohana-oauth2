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
abstract class Kohana_OAuth2_Consumer_GrantType_Authorization_Code extends OAuth2_Consumer_GrantType {

	public function request_token($grant_type_options = array())
	{
		$request = Request::factory($this->_config[$this->_provider]['token_uri'])
				->method(Request::POST)
				->post(array(
					'grant_type'    => 'authorization_code',
					'code'          => $grant_type_options['code'],
					'redirect_uri'  => URL::site($this->_config[$this->_provider]['redirect_uri'], TRUE),
					'client_id'     => $this->_config[$this->_provider]['client_id'],
					'client_secret' => $this->_config[$this->_provider]['client_secret'],
				));

		$response = $request->execute();

		if ($response->status() != 200)
		{
			throw new OAuth2_Exception_InvalidGrant('Error! .. '.$response->body());
		}

		switch ($response->headers('content-type'))
		{
			case 'application/json':
				$x = (array) json_decode($response->body());
				break;
			case 'application/x-www-form-urlencoded': # Stupid github -_-
				parse_str($response->body(), $x);
				break;
			default:
				throw new OAuth2_Exception_InvalidGrant('Unknown Content-Type: :content_type', array(
					':content_type' => $response->headers('content-type'),
				));
		}

		return $x;
	}

	public function get_redirect_uri($state = NULL, $response_type = OAuth2::RESPONSE_TYPE_CODE)
	{
		$query = http_build_query(array(
			'client_id'     => $this->_config[$this->_provider]['client_id'],
			'redirect_uri'  => URL::site($this->_config[$this->_provider]['redirect_uri'], TRUE),
			'response_type' => $response_type,
			'state'         => $state
		));

		return $this->_config[$this->_provider]['authorize_uri'].'?'.$query;
	}
}