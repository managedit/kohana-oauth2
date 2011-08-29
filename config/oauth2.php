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
	'provider' => array(
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
	),
	'consumer' => array(
		// Common Redirect URI
//		'redirect_uri' => 'http://localhost/kohana/inbound',
//		'kohana' => array(
//			// Override Redirect URI per provider
//			'redirect_uri'  => 'http://localhost/kohana/inbound/kohana',
//			'grant_type'    => OAuth2::GRANT_TYPE_AUTH_CODE,
//			'client_id'     => '113ee767-e7f8-4294-a972-80a97a7f9926',
//			'client_secret' => '36e79816-8ee1-4e4a-9f2a-8cf670861f05',
//			'authorize_uri' => 'http://www.kohanaframework.org/oauth2/authorize',
//			'token_uri'     => 'http://www.kohanaframework.org/oauth2/token',
//		),
	)
);