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
	extends Model_OAuth2
	implements Interface_Model_OAuth2_Client
{
	protected $_table_name = 'oauth2_clients';

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array(
		'id', 'user_id', 'client_id', 'client_secret', 'redirect_uri'
	);

	/**
	 * Find a client
	 *
	 * @param int    $client_id     the client to find
	 * @param string $client_secret the secret to find with
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
			$query->where('client_secret', '=', $client_secret);
		}

		$result = $query->as_object('Model_OAuth2_Client', array(
			array('loaded' => TRUE, 'saved' => TRUE)
		))->execute();

		if (count($result))
		{
			return $result->current();
		}
		else
		{
			return new Model_OAuth2_Client;
		}
	}

	/**
	 * Create a client
	 *
	 * @param string $redirect_uri sets the redirect uri
	 * @param string $user_id      sets the user id
	 *
	 * @return stdObject
	 */
	public static function create_client($redirect_uri = NULL, $user_id = NULL)
	{
		$client = new Model_OAuth2_Client(
			array(
				'data' => array(
					'user_id' => $user_id,
					'client_id' => UUID::v4(),
					'client_secret' => UUID::v4(),
					'redirect_uri' => $redirect_uri,
				)
			)
		);

		$client->save();

		return $client;
	}

	/**
	 * Deletes a token
	 *
	 * @param int $client_id client to delete
	 *
	 * @return null
	 */
	public static function delete_client($client_id)
	{
		Model_OAuth2_Client::find_client($client_id)->delete();
	}

	/**
	 * Allows us to restrict which clients can use specific
	 * response types.
	 *
	 * @return array
	 */
	public function allowed_response_types()
	{
		return $this->_config->provider['supported_response_types'];
	}
}
