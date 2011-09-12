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
abstract class Kohana_OAuth2_Consumer_GrantType {

	/**
	 * @var Config
	 */
	protected $_config;

	/**
	 * @var array Request Paramaters
	 */
	protected $_options = array();

	/**
	 * @var string Provider Name
	 */
	protected $_provider;

	public static function factory($type, $provider)
	{
		$class = 'OAuth2_Consumer_GrantType_'.$type;

		if ( ! class_exists($class))
			throw new OAuth2_Exception_UnsupportedGrantType('Unknown or invalid grant_type');

		return new $class($provider);
	}

	public function __construct($provider)
	{
		$this->_config = Kohana::$config->load('oauth2.consumer');
		$this->_provider = $provider;
	}

	abstract public function request_token($grant_type_options = array());
}