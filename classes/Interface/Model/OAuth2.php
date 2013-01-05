<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
interface Interface_Model_OAuth2
{
	/**
	 * Determines if this model is loaded
	 *
	 * @return bool
	 */
	public function loaded();
}