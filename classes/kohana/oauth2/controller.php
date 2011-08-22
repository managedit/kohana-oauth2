<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Controller
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
abstract class Kohana_OAuth2_Controller extends Controller {

	/**
	 * @var OAuth2_Provider
	 */
	protected $_oauth;

	/**
	 * @var string Client ID
	 */
	protected $_oauth_client_id = NULL;

	/**
	 * @var string User ID
	 */
	protected $_oauth_user_id = NULL;

	/**
	 * @var boolean Verify OAuth token automatically?
	 */
	protected $_oauth_verify = TRUE;

	public function before()
	{
		parent::before();

		$this->_oauth = OAuth2_Provider::factory($this->request);

		if ($this->_oauth_verify)
		{
			$this->_oauth_verify_token();
		}
	}

	protected function _oauth_verify_token($scope = NULL)
	{
		list($client_id, $user_id) = $this->_oauth->verify_token($scope);

		$this->_oauth_client_id = $client_id;
		$this->_oauth_user_id = $user_id;
	}

}