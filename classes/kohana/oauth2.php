<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Kohana_OAuth2 {
	// Validation Regexs
	const CLIENT_ID_REGEXP           = "/^[a-z0-9-_]{3,32}$/i";

	// Response Types
	const RESPONSE_TYPE_CODE = 'code';
	const RESPONSE_TYPE_TOKEN = 'token';

	public static $supported_response_types = array (
		OAuth2::RESPONSE_TYPE_CODE,
		OAuth2::RESPONSE_TYPE_TOKEN,
	);

	// Grant Types
	const GRANT_TYPE_AUTH_CODE          = 'authorization_code';
	const GRANT_TYPE_PASSWORD           = 'password';
	const GRANT_TYPE_REFRESH_TOKEN      = 'refresh_token';
	const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

	public static $supported_grant_types = array(
		OAuth2::GRANT_TYPE_AUTH_CODE,
//		OAuth2::GRANT_TYPE_PASSWORD,
//		OAuth2::GRANT_TYPE_REFRESH_TOKEN,
//		OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
	);

	// Token Types
	const TOKEN_TYPE_BEARER = 'Bearer';

	public static $supported_token_types = array (
		OAuth2::TOKEN_TYPE_BEARER,
	);

	// Error Codes
	const ERROR_INVALID_REQUEST           = 'invalid_request';
	const ERROR_INVALID_CLIENT            = 'invalid_client';
	const ERROR_INVALID_GRANT             = 'invalid_grant';
	const ERROR_UNAUTHORIZED_CLIENT       = 'unauthorized_client';
	const ERROR_REDIRECT_URI_MISMATCH     = 'redirect_uri_mismatch';
	const ERROR_ACCESS_DENIED             = 'access_denied';
	const ERROR_UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';
	const ERROR_UNSUPPORTED_GRANT_TYPE    = 'unsupported_grant_type';
	const ERROR_INVALID_SCOPE             = 'invalid_scope';

	public static $supported_scopes = array('test', 'test2');
}