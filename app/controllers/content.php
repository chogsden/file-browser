<?PHP

	// CONTENT Controller //

	// Set template from routes parameters:
	$template = 'template'.$request_parameters['route_elements']['settings']['template'];

	// Set data source:
	$database = 'es';
//	$database = 'mysql';

	// Re-set view path to template view:
	$view_path = $template;

	// Include view settings in View elements:
	$GLOBALS['view']['settings'] = $request_parameters['route_elements']['settings'];

	// Request Template controller:
	require(loadMVC('controller', $template));

	echoContent($view);

?>