<?php defined('SYSPATH') or die('No direct script access.');

include_once Kohana::find_file('vendor', 'OAuth2', 'inc');
include_once Kohana::find_file('vendor', 'OAuth2Exception', 'inc');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class OAuth2_Provider extends OAuth2 {

	public static $db_group = 'default';

	public static function factory()
	{
		return new OAuth2_Provider();
	}

	protected function getSupportedGrantTypes()
	{
		return array(
			OAUTH2_GRANT_TYPE_AUTH_CODE,
		);
	}

	public function verifyAccessToken($scope = NULL, $exit_not_present = TRUE, $exit_invalid = TRUE, $exit_expired = TRUE, $exit_scope = TRUE, $realm = NULL)
	{
		$token_param = $this->getAccessTokenParams();

		if ($token_param === FALSE) // Access token was not provided
			throw new OAuth2_Exception('The request is missing a required parameter, includes an unsupported parameter or parameter value, repeats the same parameter, uses more than one method for including an access token, or is otherwise malformed', OAuth2_Exception::INVALID_REQUEST);

		// Get the stored token data (from the implementing subclass)
		$token = $this->getAccessToken($token_param);

		if ($token === NULL)
			throw new OAuth2_Exception('The access token provided is invalid', OAuth2_Exception::INVALID_TOKEN);

		// Check token expiration (I'm leaving this check separated, later we'll fill in better error messages)
		if (isset($token["expires"]) && time() > $token["expires"])
			throw new OAuth2_Exception('The access token provided has expired', OAuth2_Exception::EXPIRED_TOKEN);

		// Check scope, if provided
		// If token doesn't have a scope, it's NULL/empty, or it's insufficient, then throw an error
		if ($scope && (!isset($token["scope"]) || !$token["scope"] || !$this->checkScope($scope, $token["scope"])))
			throw new OAuth2_Exception('The request requires higher privileges than provided by the access token', OAuth2_Exception::INSUFFICIENT_SCOPE);

		return TRUE;
	}

	protected function checkClientCredentials($client_id, $client_secret = NULL)
	{
		$query = DB::select('client_secret')
			->from('oauth2_client_secret')
			->where('client_id', '=', $client_id);

		$result = $query->execute(OAuth2_Provider::$db_group);

		if ($client_secret === NULL)
			return ($result->count() == 1);

		return $result[0]["client_secret"] == $client_secret;
	}

	protected function getRedirectUri($client_id)
	{
		$query = DB::select('redirect_uri')
			->from('oauth2_clients')
			->where('client_id', '=', $client_id);

		$result = $query->execute(OAuth2_Provider::$db_group);

		if ($result->count() != 1)
			return FALSE;

		return $result[0]['redirect_uri'];
	}

	protected function getAuthCode($code)
	{
		$query = DB::select('code', 'client_id', 'redirect_uri', 'expires', 'scope')
			->from('oauth2_auth_codes')
			->where('code', '=', $code);

		$result = $query->execute(OAuth2_Provider::$db_group)->as_array();

		return (count($result) == 1) ? $result[0] : NULL;
	}

	protected function setAuthCode($code, $client_id, $redirect_uri, $expires, $scope = NULL)
	{
		$query = DB::insert('oauth2_auth_codes', array(
			'code',
			'client_id',
			'redirect_uri',
			'expires',
			'scope',
		))->values(array(
			$code,
			$client_id,
			$redirect_uri,
			$expires,
			$scope,
		));

		$result = $query->execute();
	}

	protected function getAccessToken($oauth_token)
	{
		$query = DB::select('client_id', 'expires', 'expires')
			->from('oauth2_tokens')
			->where('oauth_token', '=', $oauth_token);

		$result = $query->execute(OAuth2_Provider::$db_group)->as_array();

		return (count($result) == 1) ? $result[0] : NULL;
	}

	protected function setAccessToken($oauth_token, $client_id, $expires, $scope = NULL)
	{
		$query = DB::insert('oauth2_tokens', array(
			'oauth_token',
			'client_id',
			'expires',
			'scope',
		))->values(array(
			$oauth_token,
			$client_id,
			$expires,
			$scope,
		));

		$result = $query->execute();
	}

}