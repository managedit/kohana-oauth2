<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Exceptions
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class OAuth2_Exception_UnsupportedGrantType extends OAuth2_Exception {
	protected $_error = OAuth2::ERROR_UNSUPPORTED_GRANT_TYPE;
}