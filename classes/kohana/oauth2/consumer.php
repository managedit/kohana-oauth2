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

	/**
	 * @var array
	 */
	protected $_token;


	public static function factory($provider, $token = NULL)
	{
		return new OAuth2_Consumer($provider, $token);
	}

	/**
	 * Constructor
	 */
	public function __construct($provider, $token = NULL)
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');
		$this->_provider = $provider;
		$this->_token = $token;
		$this->_grant_type = OAuth2_Consumer_GrantType::factory($this->_config[$provider]['grant_type'], $provider);
	}

	/**
	 * Execute an API request
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function execute(Request $request, $token = NULL)
	{
		if ($token != NULL)
		{
			$this->_token = $token;
		}

		// Dont have a token? Lets ask for one..
		if ($this->_token === NULL OR ! isset($this->_token['access_token']))
		{
			throw new OAuth2_Exception_InvalidToken('No token available');
		}

		// Try to use the token
		try
		{
			return $this->_execute($request);
		}
		catch (OAuth2_Exception_InvalidToken $e)
		{
			// Failure .. Move on
		}

		// Do we have a refresh token?
		if (isset($token['refresh_token']))
		{
			// Try to exchange a refresh token for an access token
			try
			{
				$refresh_grant_type = OAuth2_Consumer_GrantType::factory('refresh_token', $this->_provider);

				$token = $refresh_grant_type->request_token(array(
					'refresh_token' => $token['refresh_token'],
				));

				return $this->_execute($request);
			}
			catch (OAuth2_Exception_InvalidGrant $e)
			{
				throw new OAuth2_Exception_InvalidToken('No token available');
			}
		}

		// If we get here, our token and refresh token are both expired. Get another.
		throw new OAuth2_Exception_InvalidToken('No token avail');
	}

	protected function _execute($request)
	{
		$request->headers('Authorization', $this->_token['token_type'].' '.$this->_token['access_token']);

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
		$this->_token = $this->_grant_type->request_token($grant_type_options);

		return $this->_token;
	}

	public function get_grant_type()
	{
		return $this->_grant_type;
	}
}