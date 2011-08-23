<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for oauth2 access token model
 *
 * @package   OAuth2
 * @category  Model_Interface
 * @author    Managed I.T.
 * @copyright (c) 2011 Managed I.T.
 * @license   https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
interface Model_OAuth2_Interface_Access_Token
{
	/**
	 * Find an access token
	 *
	 * @param string $access_token token to find
	 * @param int    $client_id    client to match with
	 * 
	 * @return stdClass
	 */
	public static function find_token($access_token, $client_id = NULL);

	/**
	 * Create an access token
	 *
	 * @param int $client_id client id to create with
	 * @param int $user_id   user id to create with
	 * @param int $scope     scope to create with
	 * 
	 * @return stdClass
	 */
	public static function create_token(
		$client_id, $user_id = NULL, $scope = NULL
	);

	/**
	 * Deletes an access token
	 * 
	 * @param string $access_token
	 * 
	 * @return null
	 */
	public static function delete_token($access_token)
}