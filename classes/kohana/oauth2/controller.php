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

	protected $_client_id = NULL;
	protected $_user_id = NULL;

	/**
	 * @var boolean
	 */
	protected $_verify_oauth = TRUE;

	public function before()
	{
		parent::before();

		$this->_oauth = OAuth2_Provider::factory($this->request);

		if ($this->_verify_oauth)
		{
			$this->verify_token();
		}
	}

	protected function verify_token($scope = NULL)
	{
		list($client_id, $user_id) = $this->_oauth->verify_token($scope);

		$this->_client_id = $client_id;
		$this->_user_id = $user_id;
	}

}