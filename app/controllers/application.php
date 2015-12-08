<?PHP

	// APPLICATION Controller //

	// Establish client device type:
	if(!empty($config['mobile_agents'] AND !empty($_SERVER["HTTP_USER_AGENT"]))) {
		$client_device = clientDevice($config['mobile_agents'], $_SERVER["HTTP_USER_AGENT"]);
	}
//	echo($client_device);

	// Set default view path for requested route 
	// NOTE: (can be re-routed in route controller):
	$view_path =  $request_parameters['route_view'];

	// Set browser display output:
	$view['analytics'] = '';
	$view['navbar'] = '';
	$view['body'] = '';
	$view['footer'] = '';
	$view['js'] = '';

	// Request Section controller:
	require(loadMVC('controller', $request_parameters['route_request']));

	// Output route:
	if($request_parameters['output_format'] == 'html') {

		// Send the content to the view:
		require(loadMVC('view', $view_path));

		$app_title = $request_parameters['route_view'];

		// Set up navbar:
		require(loadMVC('helper', 'navbar'));

		// Get navbar display:
		require(loadMVC('view', 'shared/navbar'));

		// ADD common views in app/views/shared:
			// Get page title:
			// require(loadMVC('view', 'shared/title'));

			// Get page footer:
			// require(loadMVC('view', 'shared/footer'));

		// If html is expected, send display content to application view:
		require(loadMVC('view', 'application'));

	} else {

		// Otherwies send to alternative output controller:
		require(loadMVC('helper', $request_parameters['output_format']));

	}

?>