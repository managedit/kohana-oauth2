<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Controller
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
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
		$this->_oauth = OAuth2_Provider::factory();

		if ($this->_verify_oauth)
		{
			$this->verify_oauth();
		}
	}

	protected function verify_oauth($scope = NULL, $realm = NULL)
	{
		try
		{
			$this->_oauth->verifyAccessToken($scope, FALSE, FALSE, FALSE, FALSE, $realm);
		}
		catch (OAuth2_Excption $e)
		{
			/**
			 * @todo  Ensure all the right headers are being sent ..
			 */
			switch ($e->getCode())
			{
				case OAuth2_Exception::INVALID_REQUEST:
					throw new HTTP_Exception_400($e->getMessage());

				case OAuth2_Exception::INVALID_TOKEN:
				case OAuth2_Exception::EXPIRED_TOKEN:
					throw new HTTP_Exception_401($e->getMessage());

				case OAuth2_Exception::INSUFFICIENT_SCOPE:
					throw new HTTP_Exception_403($e->getMessage());

				default:
					throw $e;
			}
		}
	}
}