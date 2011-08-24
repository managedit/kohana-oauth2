<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Default config for the OAuth2 module
 *
 * @package   OAuth2
 * @category  Config
 * @author    Managed I.T.
 * @copyright (c) 2011 Managed I.T.
 * @license   https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
return array(
	'supported_response_types' => array(
		OAuth2::RESPONSE_TYPE_CODE,
		OAuth2::RESPONSE_TYPE_TOKEN,
	),
	'supported_grant_types' => array(
		OAuth2::GRANT_TYPE_AUTH_CODE,
		OAuth2::GRANT_TYPE_PASSWORD,
		OAuth2::GRANT_TYPE_REFRESH_TOKEN,
		OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
	),
	'supported_token_types' => array(
		OAuth2::TOKEN_TYPE_BEARER,
	),
	'scopes' => array(

	),
);