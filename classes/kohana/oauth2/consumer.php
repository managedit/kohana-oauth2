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


	public static function factory($provider, $user_id = FALSE)
	{
		return new OAuth2_Consumer($provider, $user_id);
	}
	/**
	 * Constructor
	 */
	public function __construct($provider, $user_id = FALSE)
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');
		$this->_provider = $provider;
		$this->_user_id = $user_id;

		$grant_type = $this->_config[$provider]['grant_type'];

		$this->_grant_type = OAuth2_Consumer_GrantType::factory($grant_type, $provider, $user_id);
	}

	/**
	 * Execute an API request
	 *
	 * @param Request $request
	 * @param string  $user_id
	 *
	 * @return Response
	 */
	public function execute(Request $request)
	{
		$token = Model_OAuth2_User_Token::find_token($this->_provider, $this->_user_id);

		// Dont have a token? Lets ask for one..
		if ( ! $token->loaded())
		{
			throw new OAuth2_Exception_InvalidToken('No token available for provider \':provider\' and user_id \':user_id\'', array(
				':provider' => $this->_provider,
				':user_id' => $this->_user_id,
			));
		}

		// Try to use the token
		try
		{
			return $this->_execute($request, $token);
		}
		catch (OAuth2_Exception_InvalidToken $e)
		{
			// Failure .. Move on
		}

		// Do we have a refresh token?
		if ($token->refresh_token != NULL)
		{
			// Try to exchange a refresh token for an access token
			try
			{
				$refresh_grant_type = OAuth2_Consumer_GrantType::factory('refresh_token', $this->_provider, $this->_user_id);

				$token = $refresh_grant_type->request_token($this->_user_id, array(
					'refresh_token' => $token->refresh_token,
				));

				return $this->_execute($request, $token);
			}
			catch (OAuth2_Exception_InvalidGrant $e)
			{
				throw new OAuth2_Exception_InvalidToken('No token available for provider \':provider\' and user_id \':user_id\'', array(
					':provider' => $this->_provider,
					':user_id' => $this->_user_id,
				));
			}
		}

		// If we get here, our token and refresh token are both expired. Get another.
		throw new OAuth2_Exception_InvalidToken('No token avail');
	}

	protected function _execute($request, $token)
	{
		$request->headers('Authorization', $token->token_type.' '.$token->access_token);

		$response = $request->execute();

//		if ($response->headers('WWW-Authenticate') != NULL)
		if ($response->status() == 401)
		{
			throw new OAuth2_Exception_InvalidToken('Invalid Token');
		}

		return $response;
	}

	public function request_token($grant_type_options = array())
	{
		$this->_grant_type->request_token($this->_user_id, $grant_type_options);
	}

	public function get_grant_type()
	{
		return $this->_grant_type;
	}
}