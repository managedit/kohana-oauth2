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
class Kohana_OAuth2 {
	// Response Types
	const RESPONSE_TYPE_CODE              = 'code';
	const RESPONSE_TYPE_TOKEN             = 'token';

	// Grant Types
	const GRANT_TYPE_AUTH_CODE            = 'authorization_code';
	const GRANT_TYPE_PASSWORD             = 'password';
	const GRANT_TYPE_REFRESH_TOKEN        = 'refresh_token';
	const GRANT_TYPE_CLIENT_CREDENTIALS   = 'client_credentials';

	// Token Types
	const TOKEN_TYPE_BEARER               = 'Bearer';

	// Error Codes
	const ERROR_ACCESS_DENIED             = 'access_denied';
	const ERROR_INSUFFICIENT_SCOPE        = 'insufficient_scope';
	const ERROR_INVALID_CLIENT            = 'invalid_client';
	const ERROR_INVALID_GRANT             = 'invalid_grant';
	const ERROR_INVALID_REQUEST           = 'invalid_request';
	const ERROR_INVALID_SCOPE             = 'invalid_scope';
	const ERROR_UNAUTHORIZED_CLIENT       = 'unauthorized_client';
	const ERROR_UNSUPPORTED_GRANT_TYPE    = 'unsupported_grant_type';
	const ERROR_UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';
}