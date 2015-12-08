<?PHP

	// AUTHENTXICATION controller //
	
	//	$_POST = array('url-path' => '/', 'user' => 'ucm-publisher', 'pass' => 'D1sc0veR1es');
	
	if(!empty($_POST)) {

		createLog(implode(',', $_POST));

		$user = array();

		// Check for username entry:
		if(isset($_POST['user'])) {

			// Get user accounts from model:
			$criteria['filter'] = array('username' => $_POST['user']);
			require(loadMVC('model', 'authentication'));

			// Validate username against user account:
			if(!empty($model['result']['records'])) {
				foreach($model['result']['records'] as $user_id => $user) {}
//				print_r($user);

				// Validate password against user account
				if ($user['password'] === crypt($_POST['pass'], $user['password'])) {

					// Set cookie if validated:
					setcookie($config['authentication']['cookie_name'], session_id().'-'.$user_id.'-'.$user['username'], time()+3600, "/");
					$response['status'] = 'success';
		        } else {
		        	$response['status'] = 'failed';
		        }
		    } else {
		    	$response['status'] = 'failed';
		    }

		} else {
			$response['status'] = 'failed';
		}

		// Return JSON status for ajax:
		header('Content-type: application/json');
		echo json_encode($response);

		// Unset Application view:
		$request_parameters['output_format'] = '_null';

	} else {

		// Unset Application view:
		$request_parameters['output_format'] = '_null';

		// Load LOGIN view:
		require(loadMVC('view', 'login'));

	}

?>