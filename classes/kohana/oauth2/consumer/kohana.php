<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_OAuth2_Consumer_Kohana extends OAuth2_Consumer {

	/**
	 * @var string Authorize URL
	 */
	protected $_authorize_url = 'http://www.kohanaframework.org/oauth2/authorize';

	/**
	 * @var string Token URL
	 */
	protected $_token_url     = 'http://www.kohanaframework.org/oauth2/token';

	public function __construct()
	{
		parent::__construct();

		$this->_provider      = 'kohana';
		$this->_grant_type    = $this->_config['kohana']['grant_type'];
		$this->_client_id     = $this->_config['kohana']['client_id'];
		$this->_client_secret = $this->_config['kohana']['client_secret'];

		if (isset($this->_config['kohana']['redirect_uri']))
		{
			$this->_redirect_uri = $this->_config['kohana']['redirect_uri'];
		}
	}
}