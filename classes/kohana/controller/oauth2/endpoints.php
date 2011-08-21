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

	public function action_token()
	{
		$this->response->headers('Content-Type', File::mime_by_ext('json'));
		$this->response->body($this->_oauth->token());
	}


	public function action_authorize()
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

				// authorize always ends up in a rediret .. no if's no but's..
				$redirect_url = $this->_oauth->authorize($accepted, $user->pk());

				echo $redirect_url; exit; /* Temp Hack */
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
}