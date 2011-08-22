# OAuth2 Provider

## Run the database migrations

   TODO: Detail Minion install etc etc

## Create OAuth2 client's

   TODO: Detail creating a record in the oauth2_clients table

## Provide an OAuth2 Endpoints Controller

Something along the lines of this:

	<?php defined('SYSPATH') or die('No direct script access.');

	class Controller_OAuth2_Endpoints extends Kohana_Controller_OAuth2_Endpoints {

		/**
		 * This action authenticates the resource owner and establishes whether
		 * the resource owner grants or denies the client's access request.
		 *
		 * You WILL need to extend/replace this action.
		 */
		public function action_authorize()
		{
			try
			{
				// Check if the user is logged in
				if (Auth::instance()->logged_in())
				{
					// Find the current user
					$user = Auth::instance()->get_user();

					/**
					 * Gather and validate the parameters from the query string
					 * so they can be included in the POST with the
					 * authorization results
					 */
					$auth_params = $this->_oauth->validate_authorize_params();

					/**
					 * If you want to show the name of the client requesting access,
					 * you can use this to look it up ..
					 */
					$client = Model_OAuth2_Client::find_client($auth_params['client_id']);

					/**
					 * Authorization results have been submitted. Check if
					 * the resource owner agreed, and pass this + the user's
					 * primary key into the OAuth2_Provider::authorize() method.
					 */
					if ($this->request->method() == Request::POST)
					{
						$agreed = ($this->request->post('accepted') == 'Yes');

						$redirect_url = $this->_oauth->authorize($accepted, $user->pk());

						/**
						 * Finally, Redirect the resource owner back to the
						 * client. This should be done regardless of if they
						 * granted permission or not.
						 */
						$this->request->redirect($redirect_url);
					}

					/**
					 * Show the authorization form. Ensure all the $auth_params
					 * are included as hidden fields.
					 */
					$this->response->body(View::factory('oauth2/authorize', array(
						'auth_params' => $auth_params,
						'client'      => $client,
						'user'        => $user,
					)));
				}
				else
				{
					/**
					 * Redirect the user to the login page.
					 *
					 * You should ensure that once the user has successfully
					 * logged in, redirect back to this URL ensuring ALL query
					 * string parameters are included!
					 */
					$this->request->redirect(Route::url('login'));
				}
			}
			catch (OAuth2_Exception $e)
			{
				/**
				 * Something went wrong!
				 *
				 * You should probably show a nice error page :)
				 *
				 * Do NOT redirect the user back to the client.
				 */
				throw new HTTP_Exception_400($e->getMessage());
			}
		}
	}

## Provide an OAuth2 Controller

Something along the lines of this:

	class OAuth2_Controller extends Kohana_OAuth2_Controller {

		public function before()
		{
			parent::before();

			/**
			 * Force a login as the user who granted the permission to the client.
			 */
			Auth::instance()->force_login(ORM::factory('user', $this->_oauth_user_id);

			/**
			 * Add a log entry recording which client make a request on behalf of
			 * which user. (Just a demo showing user_id and client_id acess..)
			 */
			Kohana::$log->add(Log::INFO, 'OAuth2 request made on behalf of \':user_id\' from \':client_id\', array(
				':user_id'   => $this->_oauth_user_id,
				':client_id' => $this->_oauth_client_id,
			));

		}

	}

## Create a protected resource

Something along the lines of this:

	<?php defined('SYSPATH') or die('No direct script access.');

	class Controller_Me extends OAuth2_Controller {

		public function action_index()
		{
			$user = Auth::instance()->get_user();

			$this->response->body(json_encode($user));
		}

	}