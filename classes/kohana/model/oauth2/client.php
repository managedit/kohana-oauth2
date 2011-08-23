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
	extends Database_Model
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
		$query = db::select('*')->from($this->_table)->where(
			'client_id', '=', $client_id
		);

		if (NULL !== $client_secret)
		{
			$query->where('client_id', '=', $client_secret);
		}

		$result = $query->as_object()->execute($this->_db);

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
		$token = db::insert($this->_table, $keys)
			->values($vals)
			->execute($this->_db);

		return (object) array_combine($keys, $vals);
	}

	/**
	 * Deletes a token
	 * 
	 * @return null
	 */
	public static function delete_client($client_id)
	{
		db::delete($this->_table)
			->where('client_id', '=', $client_id)
			->execute($this->_db);
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