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
		$this->_grant_type = OAuth2_Consumer_GrantType::factory($this->_config[$provider]['grant_type'], $provider);
		
		if ($token === NULL)
		{
			$this->_token = Session::instance()->get('oauth2.'.$this->_provider.'.token', NULL);
		}
		else
		{
			$this->_token = $token;
		}
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
		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Oauth2_Consumer::execute', $request->uri());
		}

		if ($token != NULL)
		{
			$this->_token = $token;
		}

		// Dont have a token? Lets ask for one..
		if ($this->_token === NULL OR ! isset($this->_token['access_token']))
		{
			if (isset($benchmark))
			{
				// Stop the benchmark
				Profiler::stop($benchmark);
			}

			throw new OAuth2_Exception_InvalidToken('No token available');
		}

		// Try to use the token
		try
		{
			$result = $this->_execute($request);
			
			if (isset($benchmark))
			{
				// Stop the benchmark
				Profiler::stop($benchmark);
			}

			return $result;
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
				
				$this->_store_token($token);

				$result = $this->_execute($request);
				
				if (isset($benchmark))
				{
					// Stop the benchmark
					Profiler::stop($benchmark);
				}

				return $result;
			}
			catch (OAuth2_Exception_InvalidGrant $e)
			{
				if (isset($benchmark))
				{
					// Stop the benchmark
					Profiler::stop($benchmark);
				}

				throw new OAuth2_Exception_InvalidToken('No token available');
			}
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
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
		$token = $this->_grant_type->request_token($grant_type_options);

		$this->_store_token($token);
		
		return $token;
	}
	
	protected function _store_token($token)
	{
		$this->_token = $token;
		
		Session::instance()->set('oauth2.'.$this->_provider.'.token', $token);
	}
	
	protected function get_token()
	{
		return $this->_token;
	}
		
	public function get_grant_type()
	{
		return $this->_grant_type;
	}
}