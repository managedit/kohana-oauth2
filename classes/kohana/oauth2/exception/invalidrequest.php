<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Exceptions
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class Kohana_OAuth2_Exception_InvalidRequest extends OAuth2_Exception {
	protected $_error = OAuth2::ERROR_INVALID_REQUEST;
}