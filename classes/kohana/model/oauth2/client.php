<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Kohana_Model_OAuth2_Client extends ORM {
	protected $_table = 'oauth2_clients';

	/**
	 * Find a client
	 *
	 *
	 * @param  string  $client_id
	 * @return Model_OAuth2_Client
	 */
	public static function find_client($client_id, $client_secret = NULL)
	{
		$client = ORM::factory('oauth2_client')
			->where('client_id', '=', $client_id);


		if ($client_secret !== NULL)
		{
			$client->where('client_secret', '=', $client_secret);
		}

		return $client->find();
	}

	/**
	 * Create a client
	 *
	 * @param  string  $client_id
	 * @param  string  $scope
	 * @return Model_OAuth2_Client
	 */
	public static function create_client($client_id, $client_secret, $redirect_uri = NULL)
	{
		$token = ORM::factory('oauth2_client')
			->values(array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri'  => $redirect_uri,
			))
			->save();

		return $token;
	}

	/**
	 * Deletes a token
	 */
	public static function delete_client($client_id)
	{
		Model_OAuth2_Client::find_client($client_id)->delete();
	}

	/**
	 * Allows us to restrict which clients can use specific
	 * response types.
	 *
	 * $response_type will be one of "code" , "token" or "code-and-token"
	 *
	 * @param  string  $response_type
	 * @return boolean
	 */
	public function allowed_response_types()
	{
		return OAuth2::$supported_response_types;
	}
}