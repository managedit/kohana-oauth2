<?php
class Controller_OAuth2 extends Controller {

	/**
	 * @var Kohana_OAuth2
	 */
	protected $_oauth;

	/**
	 * @var boolean
	 */
	protected $_verify_oauth = TRUE;

	public function before()
	{
		$this->_oauth = new Kohana_OAuth2();

		if ($this->_verify_oauth)
		{
			$this->verify_oauth();
		}
	}

	protected function verify_oauth($scope = NULL, $realm = NULL)
	{
//		verifyAccessToken($scope = NULL, $exit_not_present = TRUE, $exit_invalid = TRUE, $exit_expired = TRUE, $exit_scope = TRUE, $realm = NULL) {
		$result = $this->_oauth->verifyAccessToken($scope, TRUE, TRUE, TRUE, TRUE, $realm);

	}
}