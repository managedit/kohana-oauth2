<?php
class Controller_OAuth2_Endpoints extends Controller_OAuth2 {
	/**
	 * @var boolean
	 */
	protected $_verify_oauth = FALSE;

	public function action_authorize()
	{
		if ($this->request->method() == Request::POST)
		{
			// TODO .. This probably needs fixing ;)
			$accepted = $this->request->post('accept');
			$accepted = ($accepted == 'Yes');

			$this->_oauth->finishClientAuthorization($accepted, $this->request->post());
		}
		else
		{
			$auth_params = $this->_oauth->getAuthorizeParams();

			// TODO: Gather+Assign more useful info to the view..

			$this->response->body(View::factory('oauth2/authorize', array(
				'auth_params' => $auth_params,
			)));
		}
	}

	public function action_token()
	{
		$this->_oauth->grantAccessToken();
	}

	public function action_ping()
	{
		$this->verify_oauth(); // Manually Verify due to setting above;
		$this->response->body('pong');
	}
}