<?php defined('SYSPATH') or die('No direct script access.');

/**
 * HTTP Basic Authorization
 *
 * This could use some work (along with kohana) to support Baisc over HMVC via $request
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Provider_Authorization_Basic extends OAuth2_Provider_Authorization {

	/**
	 * Gets the client_id.
	 *
	 * @return string
	 */
	public function get_client_id()
	{
		return (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : NULL;
	}

	/**
	 * Gets the client_secret.
	 *
	 * @return string
	 */
	public function get_client_secret()
	{
		return (isset($_SERVER['PHP_AUTH_PW'])) ? $_SERVER['PHP_AUTH_PW'] : NULL;
	}
}