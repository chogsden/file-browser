<?PHP

	$pages = array('request' => 0, 'total' => 0, 'items_per_page' => 0);
	if(!empty($request_parameters['uri_elements']['page'])) {
		$pages['request'] = $request_parameters['uri_elements']['page'] - 1;
	}

//	print_r($pages);

?>