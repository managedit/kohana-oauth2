<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Controller
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Controller_OAuth2_Endpoints extends OAuth2_Controller {
	/**
	 * Disable Whole-Controller OAuth Verification to allow access to the
	 * authorize and token methods
	 *
	 * @var boolean
	 */
	protected $_verify_oauth = FALSE;

	/**
	 * This action should:
	 *
	 * 1. Present (or redirect to) a login form for unauthenticated users.
	 * 2. Check if the user has chosen to always trust the requesting site.
	 * 2.1. If Yes, Skip the form - assume a positive answer.
	 * 3. Ask the user if they trust the requesting site.
	 *
	 */
	public function action_authorize()
	{
		// Check if a form has been submitted.
		if ($this->request->method() == Request::POST)
		{
			// Does the user trust the requesting site?
			$accepted = ($this->request->post('accept') == 'Yes');

			// Should this trust be remembered?
			$remember = ($this->request->post('remember') == 'Yes');

			if ($remember)
			{
				// TODO: Implement Me Yourself!..
			}

			// Do the OAuth Checks ..
			$this->_oauth->finishClientAuthorization($accepted, $this->request->post());
		}

		// Get Authorization Parameters from the URL
		$auth_params = $this->_oauth->getAuthorizeParams();

		// Gather some useful information about the requesting site to show
		// the user.
		// TODO: Implement Me Yourself!..

		// Show the user the "yes/no/always" form.
		$this->response->body(View::factory('oauth2/authorize', array(
			'auth_params' => $auth_params,
		)));
	}

	/**
	 * Handles granting access tokens as needed.
	 * Can be left as is.
	 */
	public function action_token()
	{
		$this->_oauth->grantAccessToken();
	}

	/**
	 * Example OAuth2 that's protected manually (since this controller has
	 * contlloer-wide OAuth verification disabled)
	 */
	public function action_ping()
	{
		$this->verify_oauth(); // Manually Verify due to setting above;
		$this->response->body('pong');
	}
}