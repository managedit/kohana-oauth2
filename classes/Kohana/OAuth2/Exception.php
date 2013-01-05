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
class Kohana_OAuth2_Exception extends Kohana_Exception {
	protected $_error = NULL;

	public function getError()
	{
		return $this->_error;
	}

	public function getJsonError()
	{
		return json_encode(array(
			'error'             => $this->getError(),
			'error_description' => $this->getMessage(),
		));
	}

	public function __toString()
	{
		return $this->getJsonError();
	}
}