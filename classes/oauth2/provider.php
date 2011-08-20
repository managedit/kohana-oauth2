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
			OAUTH2_GRANT_TYPE_REFRESH_TOKEN,
			OAUTH2_GRANT_TYPE_USER_CREDENTIALS,
		);
	}

	public function verifyAccessToken($scope = NULL, $exit_not_present = TRUE, $exit_invalid = TRUE, $exit_expired = TRUE, $exit_scope = TRUE, $realm = NULL)
	{
		$token_param = $this->getAccessTokenParams();

		if ($token_param === FALSE) // Access token was not provided
			throw new OAuth2_Exception('The request is missing a required parameter, includes an unsupported parameter or parameter value, repeats the same parameter, uses more than one method for including an access token, or is otherwise malformed', NULL, OAuth2_Exception::INVALID_REQUEST);

		// Get the stored token data (from the implementing subclass)
		$token = $this->getAccessToken($token_param);

		if ($token === NULL)
			throw new OAuth2_Exception('The access token provided is invalid', NULL, OAuth2_Exception::INVALID_TOKEN);

		// Check token expiration (I'm leaving this check separated, later we'll fill in better error messages)
		if (isset($token["expires"]) && time() > $token["expires"])
			throw new OAuth2_Exception('The access token provided has expired', NULL, OAuth2_Exception::EXPIRED_TOKEN);

		// Check scope, if provided
		// If token doesn't have a scope, it's NULL/empty, or it's insufficient, then throw an error
		if ($scope && (!isset($token["scope"]) || !$token["scope"] || !$this->checkScope($scope, $token["scope"])))
			throw new OAuth2_Exception('The request requires higher privileges than provided by the access token', NULL, OAuth2_Exception::INSUFFICIENT_SCOPE);

		return TRUE;
	}

	protected function checkClientCredentials($client_id, $client_secret = NULL)
	{
		$query = DB::select('client_secret')
			->from('oauth2_clients')
			->where('client_id', '=', $client_id);

		$result = $query->execute(OAuth2_Provider::$db_group);

		if ($client_secret === NULL)
			return ($result->count() == 1);

		return $result[0]["client_secret"] == $client_secret;
	}

	/**
	 * Grant access tokens for basic user credentials.
	 *
	 * Check the supplied username and password for validity.
	 *
	 * You can also use the $client_id param to do any checks required based
	 * on a client, if you need that.
	 *
	 * Required for OAUTH2_GRANT_TYPE_USER_CREDENTIALS.
	 *
	 * @param   $client_id  Client identifier to be check with.
	 * @param   $username   Username to be check with.
	 * @param   $password   Password to be check with.
	 * @return  boolean
	 *	 TRUE if the username and password are valid, and FALSE if it isn't.
	 *	 Moreover, if the username and password are valid, and you want to
	 *	 verify the scope of a user's access, return an associative array
	 *	 with the scope values as below. We'll check the scope you provide
	 *	 against the requested scope before providing an access token:
	 * @code
	 * return array(
	 *	 'scope' => <stored scope values (space-separated string)>,
	 * );
	 * @endcode
	 *
	 * @link    http://tools.ietf.org/html/draft-ietf-oauth-v2-10#section-4.1.2
	 * @ingroup oauth2_section_4
	 */
	protected function checkUserCredentials($client_id, $username, $password)
	{
		return Auth::instance()->login($username, $password);
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

	/**
	 * Grant refresh access tokens.
	 *
	 * Retrieve the stored data for the given refresh token.
	 *
	 * Required for OAUTH2_GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @param    $refresh_token  Refresh token to be check with.
	 * @link     http://tools.ietf.org/html/draft-ietf-oauth-v2-10#section-4.1.4
	 * @return   array
	 *	 An associative array as below, and NULL if the refresh_token is
	 *	 invalid:
	 *	 - client_id: Stored client identifier.
	 *	 - expires: Stored expiration unix timestamp.
	 *	 - scope: (optional) Stored scope values in space-separated string.
	 *
	 * @ingroup  oauth2_section_4
	 */
	protected function getRefreshToken($refresh_token)
	{
		$query = DB::select('client_id', 'expires', 'expires')
			->from('oauth2_refresh_tokens')
			->where('refresh_token', '=', $refresh_token);

		$result = $query->execute(OAuth2_Provider::$db_group)->as_array();

		return (count($result) == 1) ? $result[0] : NULL;
	}

	/**
	 * Take the provided refresh token values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getRefreshToken().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAUTH2_GRANT_TYPE_REFRESH_TOKEN.
	 *
	 * @param   $refresh_token    Refresh token to be stored.
	 * @param   $client_id        Client identifier to be stored.
	 * @param   $expires          expires to be stored.
	 * @param   $scope            Scopes to be stored in space-separated string. (optional)
	 * @ingroup oauth2_section_4
	*/
	protected function setRefreshToken($refresh_token, $client_id, $expires, $scope = NULL)
	{
		$query = DB::insert('oauth2_refresh_tokens', array(
			'refresh_token',
			'client_id',
			'expires',
			'scope',
		))->values(array(
			$refresh_token,
			$client_id,
			$expires,
			$scope,
		));

		$result = $query->execute();
	}

	/**
	 * Expire a used refresh token.
	 *
	 * This is not explicitly required in the spec, but is almost implied.
	 * After granting a new refresh token, the old one is no longer useful and
	 * so should be forcibly expired in the data store so it can't be used again.
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * @param    $refresh_token    Refresh token to be expirse.
	 * @ingroup  oauth2_section_4
	 */
	protected function unsetRefreshToken($refresh_token)
	{
		DB::delete('oauth2_refresh_tokens')
			->where('refresh_token', '=', $refresh_token)
			->execute();
	}
}