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
abstract class Kohana_OAuth2_Consumer {

	public static function factory($provider)
	{
		$class = 'OAuth2_Consumer_'.$provider;

		return new $class($provider);
	}

	/**
	 * @var Config Configuration
	 */
	protected $_config;

	/**
	 * @var string Provider Name
	 */
	protected $_provider = NULL;

	/**
	 * @var string Authorize URL
	 */
	protected $_authorize_url = NULL;

	/**
	 * @var string Token URL
	 */
	protected $_token_url     = NULL;

	/**
	 * @var string Grant Type to use
	 */
	protected $_grant_type    = NULL;

	/**
	 * @var string Client ID
	 */
	protected $_client_id     = NULL;

	/**
	 * @var string Client Secret
	 */
	protected $_client_secret = NULL;

	/**
	 * @var string Redirect URL
	 */
	protected $_redirect_uri = NULL;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');

		$this->_redirect_uri = $this->_config['redirect_uri'];
	}

	/**
	 * Execute an API request
	 * @param Request $request
	 * @param string  $user_id
	 *
	 * @return Response
	 */
	public function execute(Request $request, $user_id = NULL)
	{
		$token = Model_OAuth2_User_Token::find_token($this->_provider, $user_id);

		if ( ! $token->loaded())
			throw new OAuth2_Exception_InvalidGrant('Need to obtain new tokens');

		$request->headers('Authorization', $token->token_type.' '.$token->access_token);

		$response = $request->execute();

		if ($response->status() != 200)
			$this->_exchange_refresh_token_for_access_token($token);

		$token = Model_OAuth2_User_Token::find_token($this->_provider, $user_id);

		$request->headers('Authorization', $token->token_type.' '.$token->access_token);

		$response = $request->execute();

		if ($response->status() != 200)
			throw new OAuth2_Exception_InvalidGrant('Need to obtain new tokens');

		return $response;
	}

	/**
	 * Exchanges a authorization_code for an access token
	 *
	 * @param  Request $request
	 * @return Model_OAuth2_User_Token
	 */
	protected function _exchange_refresh_token_for_access_token($token)
	{
		if ( ! Valid::not_empty($token->refresh_token))
			throw new OAuth2_Exception_InvalidToken('No token, need to authorize.');

		$token_request = Request::factory($this->_token_url)
			->method(Request::POST);

		$token_request->post('client_id', $this->_client_id);
		$token_request->post('client_secret', $this->_client_secret);
		$token_request->post('grant_type', 'refresh_token');
		$token_request->post('refresh_token', $token->refresh_token);

		$response = $token_request->execute();

		if ($response->status() != 200)
		{
			throw new OAuth2_Exception_InvalidGrant('Unable to exchange refresh token for access token');
		}

		switch ($response->headers('content-type'))
		{
			case 'application/json':
				$x = (array) json_decode($response->body());
				break;
			case 'application/x-www-form-urlencoded': # Stupid github -_-
				parse_str($response->body(), $x);
				break;
		}

		// Lazy..
		$user_id = $token->user_id;

		$token_type = @$x['token_type'];
		$access_token = @$x['access_token'];
		$refresh_token = @$x['refresh_token'];

		$token = Model_OAuth2_User_Token::create_token($this->_provider, $token_type, $access_token, $user_id, $refresh_token);

		return $token;
	}

	/**
	 * Exchanges a authorization_code for an access token
	 *
	 * @param  Request $request
	 * @return Model_OAuth2_User_Token
	 */
	public function exchange_code_for_token($request)
	{
		$token_request = Request::factory($this->_token_url)
			->method(Request::POST);

		$token_request->post('client_id', $this->_client_id);
		$token_request->post('client_secret', $this->_client_secret);
		$token_request->post('redirect_uri', $this->_redirect_uri);
		$token_request->post('grant_type', 'authorization_code');
		$token_request->post('code', $request->query('code'));

		$response = $token_request->execute();

		if ($response->status() != 200)
		{
			throw new OAuth2_Exception_InvalidGrant('Something went wrong while getting a token!');
		}

		switch ($response->headers('content-type'))
		{
			case 'application/json':
				$x = (array) json_decode($response->body());
				break;
			case 'application/x-www-form-urlencoded': # Stupid github -_-
				parse_str($response->body(), $x);
				break;
		}

		// Lazy..
		$user_id = 1;
		$token_type = @$x['token_type'];
		$access_token = @$x['access_token'];
		$refresh_token = @$x['refresh_token'];

		$token = Model_OAuth2_User_Token::create_token($this->_provider, $token_type, $access_token, $user_id, $refresh_token);

		return $token;
	}


	/**
	 * Generates the authorize URL
	 *
	 * @param string $state State to be passed through the authorize request
	 * @return string
	 */
	public function authorize_url($state = NULL)
	{
		return $this->_authorize_url.'?response_type=code&client_id='.$this->_client_id.'&redirect_uri='.urlencode($this->_redirect_uri).'&state='.urlencode($state);
	}
}