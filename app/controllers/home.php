<?PHP

	// HOME Controller //

	$content = array();

	echoContent($content);

	// Send the content to the view:
	require(loadMVC('view', 'home'));

?>