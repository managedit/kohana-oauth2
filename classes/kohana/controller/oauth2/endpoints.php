<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
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
		try
		{
			// Assume the user agreed, provide a NULL user_id and authorize.
			$redirect_url = $this->_oauth->authorize(TRUE, NULL);

			// Redirect the user back to the application
			$this->request->redirect($redirect_url);
		}
		catch (OAuth2_Exception $e)
		{
			throw new HTTP_Exception_400($e->getMessage());
		}
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
			$this->response->body($e->getJsonError());
		}
	}
}