<?PHP

	// Load application core config settings and shared scripts:
	require_once('config/global.php');
	require_once('app/core/functions.php');

	session_start();

	global $argv;
	$request_parameters = clientRequest($config);

//	$view_path = '';

	// Load Application helper:
	require(loadMVC('helper', 'application'));

	// Load Application controller:
	require_once(loadMVC('controller', $GLOBALS['controller']));

?>