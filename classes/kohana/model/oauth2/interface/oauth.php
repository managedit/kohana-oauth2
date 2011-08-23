<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Base model for oauth2 models
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
interface Kohana_Model_OAuth2_Interface_Oauth
{
	/**
	 * Determines if this model is loaded
	 *
	 * @return bool
	 */
	public function loaded();
}