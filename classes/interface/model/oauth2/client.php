<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Models an oauth client to insert, read and delete data
 *
 * @package   OAuth2
 * @category  Model_Interface
 * @author    Managed I.T.
 * @copyright (c) 2011 Managed I.T.
 * @license   https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
interface Interface_Model_OAuth2_Client extends Interface_Model_OAuth2
{
	/**
	 * Find a client
	 *
	 * @param string $client_id
	 *
	 * @return stdClass | null
	 */
	public static function find_client($client_id, $client_secret = NULL);

	/**
	 * Create a client
	 *
	 * @param string $redirect_uri sets the redirect uri
	 * @param string $user_id      sets the user id
	 *
	 * @return stdObject
	 */
	public static function create_client(
		$redirect_uri = NULL, $user_id = NULL
	);

	/**
	 * Deletes a token
	 *
	 * @return null
	 */
	public static function delete_client($client_id);

	/**
	 * Allows us to restrict which clients can use specific
	 * response types.
	 *
	 * @return array
	 */
	public function allowed_response_types();
}