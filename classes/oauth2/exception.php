<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Controller
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 */
class OAuth2_Exception extends Kohana_Exception {
	const INVALID_REQUEST = 1;
	const INVALID_TOKEN = 2;
	const EXPIRED_TOKEN = 3;
	const INSUFFICIENT_SCOPE = 4;
}