<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class OAuth2_Provider {

	public static function factory(Request $request)
	{
		return new OAuth2_Provider($request);
	}

	/**
	 * @var Request HTTP Request
	 */
	protected $_request;

	public function __construct(Request $request)
	{
		$this->_request = $request;
	}

	/**
	 *
	 * @return array
	 */
	protected function _get_authorize_params()
	{
		$input = array();

		if ($this->_request->method() == Request::GET)
		{
			$input = $this->_request->query();
		}
		else
		{
			$input = $this->_request->post();
		}

		return Arr::extract($input, array(
			'client_id',
			'response_type',
			'redirect_uri',
			'state',
			'scope',
		));
	}

	/**
	 *
	 * @return array
	 */
	public function validate_authorize_params()
	{
		$request_params = $this->_get_authorize_params();

		$validation = Validation::factory($request_params)
			->rule('client_id',     'not_empty')
			->rule('client_id',     'regex',     array(':value', OAuth2::CLIENT_ID_REGEXP))
			->rule('response_type', 'not_empty')
			->rule('response_type', 'in_array',  array(':value', OAuth2::$supported_response_types))
			->rule('scope',         'in_array',  array(':value', OAuth2::$supported_scopes))
			->rule('redirect_uri',  'url');

		if ( ! $validation->check())
		{
			// TODO: Add a message...
			throw new OAuth2_Exception_InvalidRequest("Invalid Request");
		}

		// Check we have a valid client
		$client = Model_OAuth2_Client::find_client($request_params['client_id']);

		if ( ! $client->loaded())
		{
			throw new OAuth2_Exception_InvalidClient('Invalid client');
		}

		// Lookup the redirect_uri if none was supplied in the URL
		if ( ! Valid::url($request_params['redirect_uri']))
		{
			$request_params['redirect_uri'] = $client->redirect_uri;

			// Is the redirect_uri still empty? Error if so..
			if ( ! Valid::url($request_params['redirect_uri']))
				throw new OAuth2_Exception_InvalidRequest('\'redirect_uri\' is required');
		}

		// Check if this client is allowed use this response_type
		if ( ! in_array($request_params['response_type'], $client->allowed_response_types()))
			throw new OAuth2_Exception_UnauthorizedClient('You are not allowed use the \':response_type\' response_type', array(
				':response_type' => $request_params['response_type']
			));

		return $request_params;
	}

	/**
	 * @return string Redirect URL
	 */
	public function authorize($accepted, $user_id = NULL)
	{
		// Validate the request
		$request_params = $this->validate_authorize_params();


		// Find the client
		$client = Model_OAuth2_Client::find_client($request_params['client_id']);

		$url  = $request_params['redirect_uri'];

		if ( ! $accepted)
		{
			$url .= 'error='.OAuth2::ERROR_ACCESS_DENIED;

			if (Valid::not_empty($request_params['state']))
			{
				$url .= '&state='.urlencode($request_params['state']);
			}
		}
		else
		{
			// Generate a code...
			$auth_code = Model_OAuth2_Auth_Code::create_code($request_params['client_id'], $request_params['redirect_uri'], $user_id, $request_params['scope']);

			if ($request_params['response_type'] == OAuth2::RESPONSE_TYPE_CODE)
			{
				$url .= '?code='.urlencode($auth_code->code);

				if (Valid::not_empty($request_params['state']))
				{
					$url .= '&state='.urlencode($request_params['state']);
				}

				if (Valid::not_empty($request_params['scope']))
				{
					$url .= '&scope='.urlencode($request_params['scope']);
				}
			}
			else if ($request_params['response_type'] == OAuth2::RESPONSE_TYPE_TOKEN)
			{
				// Generate an access token
				$access_token = Model_OAuth2_Access_Token::create_token($request_params['client_id'], $user_id, $request_params['scope']);

				$url .= '#access_token='.$access_token->access_token.'&token_type='.OAuth2::TOKEN_TYPE_BEARER;

				if (Valid::not_empty($request_params['state']))
				{
					$url .= '&state='.urlencode($request_params['state']);
				}

				if (Valid::not_empty($request_params['scope']))
				{
					$url .= '&scope='.urlencode($request_params['scope']);
				}
			}
			else
			{
				throw new OAuth2_Exception_InvalidRequest('Unsupported response_type');
			}

		}

		// Return the redirect URL
		return $url;
	}

	/**
	 *
	 * @return array
	 */
	protected function _get_token_params()
	{
		$input = array();

		if ($this->_request->method() == Request::GET)
		{
			$input = $this->_request->query();
		}
		else
		{
			$input = $this->_request->post();
		}

		return Arr::extract($input, array(
			'client_id',
			'client_secret',
			'grant_type',    // refresh_token, authorization_code, password, client_credentials
			'refresh_token', // refresh_token,
			'code',          // authorization_code,
			'username',      // password,
			'password',      // password,
			'scope',         // refresh_token, password, client_credentials
			'redirect_uri',  // authorization_code,
		));
	}

	public function validate_token_params()
	{
		$request_params = $this->_get_token_params();

		$validation = Validation::factory($request_params)
			->rule('client_id',     'not_empty')
			->rule('client_id',     'regex',    array(':value', OAuth2::CLIENT_ID_REGEXP))
			->rule('client_secret', 'not_empty')
			->rule('grant_type',    'not_empty')
			->rule('grant_type',    'in_array', array(':value', OAuth2::$supported_grant_types))
//			->rule('refresh_token', 'uuid'),
//			->rule('code',          'uuid'),
			->rule('scope',         'in_array', array(':value', OAuth2::$supported_scopes))
			->rule('redirect_uri',  'url');

		if ( ! $validation->check())
		{
			// TODO: Add a better message...
			echo Debug::vars($validation->errors());
			throw new OAuth2_Exception_InvalidRequest("Invalid Request");
		}

		// Find the client
		$client = Model_OAuth2_Client::find_client($request_params['client_id'], $request_params['client_secret']);

		if ( ! $client->loaded())
			throw new OAuth2_Exception_UnauthorizedClient('Unauthorized Client');


		if ($request_params['grant_type'] == OAuth2::GRANT_TYPE_AUTH_CODE)
		{
			if ( ! Valid::not_empty($request_params['code']))
			{
				throw new OAuth2_Exception_InvalidGrant('code is required with the '.OAuth2::GRANT_TYPE_AUTH_CODE.' grant_type');
			}

			// Ensure the code is still valid, and that it was issued to the requesting client_id
			if ( ! Model_OAuth2_Auth_Code::validate_code($request_params['code'], $client->client_id))
				throw new OAuth2_Exception_InvalidGrant('Invalid Grant');

			if ( ! Valid::not_empty($request_params['redirect_uri']))
			{
				throw new OAuth2_Exception_InvalidRequest('redirect_uri is required with the '.OAuth2::GRANT_TYPE_AUTH_CODE.' grant_type');
			}

			$auth_code = Model_OAuth2_Auth_Code::find_code($request_params['code']);

			if ($auth_code->redirect_uri !== $request_params['redirect_uri'])
			{
				throw new OAuth2_Exception_InvalidGrant('redirect_uri mismatch');
			}

			if ($auth_code->scope !== $request_params['scope'])
			{
				throw new OAuth2_Exception_InvalidGrant('scope mismatch');
			}
		}

		return $request_params;
	}

	public function token()
	{

		// Validate the request
		$request_params = $this->validate_token_params();

		// Response Params
		$response_params = array(
			'token_type'    => OAuth2::TOKEN_TYPE_BEARER,
			'expires_in'    => Model_OAuth2_Access_Token::$lifetime,
		);

		$client = Model_OAuth2_Client::find_client($request_params['client_id'], $request_params['client_secret']);

		$user_id = NULL;

		if ($request_params['grant_type'] == OAuth2::GRANT_TYPE_AUTH_CODE)
		{
			$auth_code = Model_OAuth2_Auth_Code::find_code($request_params['code']);
			$user_id = $auth_code->user_id;
		}
		else
		{
			throw new OAuth2_Exception_UnsupportedGrantType('Unsupported Grant Type');
		}


		// Generate an access token
		$access_token = Model_OAuth2_Access_Token::create_token($request_params['client_id'], $user_id, $request_params['scope']);

		$response_params['access_token'] = $access_token->access_token;

		// If refreh tokens are supported, add one.
		if (in_array(OAuth2::GRANT_TYPE_REFRESH_TOKEN, OAuth2::$supported_grant_types))
		{
			// Generate a refresh token
			$refresh_token = Model_OAuth2_Refresh_Token::create_token($request_params['client_id'], $user_id, $request_params['scope']);

			$response_params['refresh_token'] = $refresh_token->refresh_token;
		}

		// Add scope if needed
		if (Valid::not_empty($request_params['scope']))
		{
			$response_params['scope'] = $request_params['scope'];
		}

		return json_encode($response_params);
	}
}