<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Library
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Kohana_Controller_OAuth2_Endpoints extends Controller {
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
	 * It likely you WILL need to extend/replace this action.
	 */
	public function action_authorize()
	{
		try
		{
			Auth::instance()->force_login(ORM::factory('user', 1));

			/**
			 * Check if the user is logged in
			 */
			if (Auth::instance()->logged_in())
			{
				$user = Auth::instance()->get_user();

				$auth_params = $this->_oauth->validate_authorize_params();


				// Form has been submitted
				if ($this->request->method() == Request::POST)
				{
					$accepted = ($this->request->post('accepted') == 'Yes');
					$accepted = TRUE;

					// Validate custom form stuff .. whatever
					$redirect_url = $this->_oauth->authorize($accepted, $user->pk());

					// Redirect the user back to the application
					$this->request->redirect($redirect_url);
				}


				$client = Model_OAuth2_Client::find_client($auth_params['client_id']);

				$this->response->body(View::factory('oauth2/authorize', array(
					'auth_params' => $auth_params,
					'client'      => $client,
					'user'        => $user,
				)));
			}
			else
			{
				$this->request->redirect(Route::url('login'));
			}
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
	 * Its unlikely you will need to extend/replace this action.
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