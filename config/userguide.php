<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'oauth2' => array(

			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,

			// The name that should show up on the userguide index page
			'name' => 'OAuth2',

			// A short description of this module, shown on the index page
			'description' => 'OAuth 2',

			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–201'.date('Y').' Managed I.T.',
		)
	)
);