<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Exceptions
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
class Kohana_OAuth2_Exception_InvalidGrant extends OAuth2_Exception {
	protected $_error = OAuth2::ERROR_INVALID_GRANT;

	protected $_redirect_uri;

	public function setRedirectUri($redirect_uri = NULL)
	{
		$this->_redirect_uri = $redirect_uri;
	}

	public function getRedirectUri($state = NULL)
	{
		// TODO: State
		return $this->_redirect_uri;
	}
}