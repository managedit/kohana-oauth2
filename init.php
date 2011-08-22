<?php

Route::set('oauth2', 'oauth2/<action>')
	->defaults(array(
		'directory'  => 'oauth2',
		'controller' => 'endpoints',
	));