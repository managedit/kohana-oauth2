<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Controller_Test extends OAuth2_Controller {

	public function action_ping()
	{
		$this->response->body('pong to client '.$this->_client_id. ' on behalf of user '.$this->_user_id);
	}

}