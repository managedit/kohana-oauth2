<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
abstract class Kohana_Controller_OAuth2_Endpoints extends Controller {
	/**
	 * @var OAuth2_Provider
	 */
	protected $_oauth;

	public function before()
	{
		parent::before();

		$this->_oauth = OAuth2_Provider::factory($this->request);
	}

	/**
	 * This action authenticates the resource owner and establishes whether
	 * the resource owner grants or denies the client's access request.
	 *
	 * You WILL need to extend/replace this action.
	 */
	public function action_authorize()
	{
		throw new Kohana_Exception('The Controller_OAuth2_Endpoints::action_authorize() method needs to be implemented');
	}

	/**
	 * This action issues access and refresh tokens and is called only
	 * by the 3rd party. All output should be JSON.
	 *
	 * You DO NOT need to extend/replace this action.
	 */
	public function action_token()
	{
		$this->response->headers('Content-Type', File::mime_by_ext('json'));

		try
		{
			// Attempt to issue a token
			$this->response->body($this->_oauth->token());
		}
		catch (OAuth2_Exception $e)
		{
			// Something went wrong, lets give a formatted error
			$this->response->status(400);
			$this->response->headers('WWW-Authenticate', 'Bearer');
			$this->response->body($e->getJsonError());
		}
	}
}