<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Models an oauth client to insert, read and delete data
 *
 * @package   OAuth2
 * @category  Model
 * @author    Managed I.T.
 * @copyright (c) 2011 Managed I.T.
 * @license   https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_Model_OAuth2_Client
	extends Model_Database
	implements Kohana_Model_OAuth2_Interface_Client
{
	protected $_table = 'oauth2_clients';

	/**
	 * Find a client
	 *
	 * @param string $client_id
	 * 
	 * @return stdClass | null
	 */
	public static function find_client($client_id, $client_secret = NULL)
	{
		$query = db::select('*')->from('oauth2_clients')->where(
			'client_id', '=', $client_id
		);

		if (NULL !== $client_secret)
		{
			$query->where('client_id', '=', $client_secret);
		}

		$result = $query->as_object()->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Create a client
	 *
	 * @param string $client_id    sets the client id
	 * @param string $scope        sets the scope
	 * @param string $redirect_uri sets the redirect uri
	 * 
	 * @return stdObject
	 */
	public static function create_client(
		$client_id, $client_secret, $redirect_uri = NULL
	)
	{
		$keys = array('client_id', 'client_secret', 'redirect_uri');
		$vals = array($client_id, $client_secret, $redirect_uri);
		$token = db::insert('oauth2_clients', $keys)
			->values($vals)
			->execute();

		return (object) array_combine($keys, $vals);
	}

	/**
	 * Deletes a token
	 * 
	 * @return null
	 */
	public static function delete_client($client_id)
	{
		db::delete('oauth2_clients')
			->where('client_id', '=', $client_id)
			->execute();
	}

	/**
	 * Allows us to restrict which clients can use specific
	 * response types.
	 * 
	 * @return array
	 */
	public function allowed_response_types()
	{
		return OAuth2::$supported_response_types;
	}
}