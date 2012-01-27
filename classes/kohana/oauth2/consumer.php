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

	/**
	 * OAuth2 Factory
	 * 
	 * @param string $provider  Provider name
	 * @param array  $token     Optional token to use
	 */
	public static function factory($provider, $token = NULL)
	{
		return new OAuth2_Consumer($provider, $token);
	}

	/**
	 * OAuth2 Constructor
	 * 
	 * @param string $provider  Provider name
	 * @param array  $token     Optional token to use
	 */
	public function __construct($provider, $token = NULL)
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');
		$this->_provider = $provider;
		$this->_grant_type = OAuth2_Consumer_GrantType::factory($this->_config[$provider]['grant_type'], $provider);
		
		if ($token === NULL)
		{
			$this->_token = $this->_retrieve_token();
		}
		else
		{
			$this->_token = $token;
		}
	}

	/**
	 * Execute an API request
	 *
	 * @param Request  $request  Request to be executed
	 * @param array    $token    Optional token to use
	 * @return Response
	 */
	public function execute(Request $request, $token = NULL)
	{
		Kohana::$log->add(Log::DEBUG, "OAuth2: Attempting to make request");
		
		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Oauth2_Consumer::execute', $request->uri());
		}

		if ($token != NULL)
		{
			$this->_token = $token;
		}

		// Dont have a token? Oh well..
		if ($this->_token === NULL OR ! isset($this->_token['access_token']))
		{
			if (isset($benchmark))
			{
				// Stop the benchmark
				Profiler::stop($benchmark);
			}
			
			Kohana::$log->add(Log::DEBUG, "OAuth2: No token available");
			
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
			Kohana::$log->add(Log::DEBUG, "OAuth2: access_token invalid. Checking for refresh_token.");
			// Failure .. Move on
		}

		// Do we have a refresh token?
		if (isset($this->_token['refresh_token']))
		{
			Kohana::$log->add(Log::DEBUG, "OAuth2: refresh_token available. Attemping to exchange it for a fresh access_token.");
			// Try to exchange a refresh token for an access token
			try
			{
				$this->exchange_refresh_token();
				
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
				
				Kohana::$log->add(Log::DEBUG, "OAuth2: refresh_token invalid.");
				
				throw new OAuth2_Exception_InvalidToken('No token available');
			}
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		// If we get here, our token and refresh token are both expired.
		Kohana::$log->add(Log::DEBUG, "OAuth2: Ran out of options, unable to execute request.");
		
		throw new OAuth2_Exception_InvalidToken('No token available');
	}

	/**
	 * Executes the given request
	 * @param  Request   $request  Request to be executed
	 * @return Response
	 */
	protected function _execute($request)
	{
		$request->headers('Authorization', $this->_token['token_type'].' '.$this->_token['access_token']);
		
		try
		{
			$response = $request->execute();
		}
		catch (HTTP_Exception_401 $e)
		{
			// Dirty hack to work-around a Kohana HVMC request isolation bug.
			throw new OAuth2_Exception_InvalidToken('Invalid Token');
		}

//		if ($response->headers('WWW-Authenticate') != NULL)
		if ($response->status() == 401)
		{
			throw new OAuth2_Exception_InvalidToken('Invalid Token');
		}

		return $response;
	}

	/**
	 * Given a set of grant type options, obtains a token from the provider.
	 * 
	 * @param array $grant_type_options Array of options, specific to each grant type.
	 */
	public function request_token($grant_type_options = array())
	{
		$this->_token = $this->_grant_type->request_token($grant_type_options);

		$this->_store_token($this->_token);
		
		return $this->_token;
	}

	/**
	 * Exchanges a refresh token for a new access token.
	 */
	public function exchange_refresh_token()
	{
		$refresh_grant_type = OAuth2_Consumer_GrantType::factory('refresh_token', $this->_provider);

		$this->_token = $refresh_grant_type->request_token(array(
			'refresh_token' => $this->_token['refresh_token'],
		));

		$this->_store_token($this->_token);
		
		return $this->_token;
	}

	/**
	 * Used to retrieve a token from persistent storage.
	 * 
	 * Defaults to storing the token in the users Session.
	 */
	protected function _retrieve_token()
	{
		return Session::instance()->get('oauth2.'.$this->_provider.'.token', NULL);
	}
	
	/**
	 * Used to save a token to persistent storage.
	 * 
	 * Defaults to storing the token in the users Session.
	 */
	protected function _store_token($token)
	{
		Session::instance()->set('oauth2.'.$this->_provider.'.token', $token);
	}
	
	/**
	 * Accessor method for retrieving to the current token.
	 * 
	 * @todo Is this really needed? Remove if not.
	 */
	public function get_token()
	{
		return $this->_token;
	}

	/**
	 * Accessor method for retrieving to the current grant type.
	 * 
	 * @todo Is this really needed? Remove if not.
	 */
	public function get_grant_type()
	{
		return $this->_grant_type;
	}

	/**
	 * Accessor method for retrieving to the APIs base URI
	 */
	public function base_uri()
	{
		return $this->_config[$this->_provider]['base_uri'];
	}
}