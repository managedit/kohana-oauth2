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

	/**
	 * @var Config Configuration
	 */
	protected $_config;

	/**
	 * @var OAuth2_Consumer_GrantType
	 */
	protected $_grant_type;

	/**
	 * @var string
	 */
	protected $_provider;


	public static function factory($provider, $grant_type, $grant_type_options = array())
	{
		return new OAuth2_Consumer($provider, $grant_type, $grant_type_options);
	}

	/**
	 * Constructor
	 */
	public function __construct($provider, $grant_type, $grant_type_options = array())
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');
		$this->_provider = $provider;
		$this->_grant_type = OAuth2_Consumer_GrantType::factory($grant_type, $grant_type_options, $provider);
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

		// Dont have a token? Lets ask for one..
		if ( ! $token->loaded())
		{
			$token = $this->_grant_type->request_token($user_id);
		}

		try
		{
			$response = $this->_execute($request, $token);
		}
		catch (OAuth2_Exception_InvalidToken $e)
		{
			// If we dont have a refresh token, there is nothing to retry.
			if ($token->refresh_token == NULL)
				throw $e;

			// The token was invalid/expired. Use our refresh token to get a new one..
			$refresh_grant_type = OAuth2_Consumer_GrantType::factory('refresh_token', array(
				'refresh_token' => $token->refresh_token,
			), $this->_provider);

			$token = $refresh_grant_type->request_token($user_id);

			$response = $this->_execute($request, $token);
		}

		return $response;
	}

	protected function _execute($request, $token)
	{
		$request->headers('Authorization', $token->token_type.' '.$token->access_token);

		$response = $request->execute();

		if ($response->headers('WWW-Authenticate') != NULL)
		{
			throw new OAuth2_Exception_InvalidToken('Invalid Token');
		}

		return $response;
	}
}