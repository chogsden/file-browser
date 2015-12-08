<?PHP
	// TEMPLATE 5 Controller //

	// Set Model query type:
		if($database == 'es') {

			$id_field = 'id';

			// Set Model query type:
			$model[$controller]['selector'] = 'SEARCH_INDEX_all';

			$criteria['filter'] = array('section_id' => $request_parameters['client_request']);

		} elseif($database == 'mysql') {

			$id_field = 'content_id';

			// Set Model query type:
			$model[$controller]['selector'] = 'SEARCH_all';

			$criteria['filter'] = array('section_id = "'.$request_parameters['client_request'].'"');

		}

		// Set Model query elements:

		$model[$controller]['criteria'] = array(

			// Set record return limit criteria for db query:
			'limit' => array(0, 100),

			// Set query filter:
			'filter' => $criteria['filter'],

			// Set fields to return from model:
			'return' => array('name', 'type', 'content'),

			// Set order of returned records:
			'order' => array('parent', 'order_by'),

			// Send report to commandline:
			'report' => true

		);

	// Get data from the SECTION model:
	$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
	require(loadMVC('model', 'content'));

	// BUILD CONTENT FOR VIEW:
	$view[$controller]['title'] = $request_parameters['route_elements']['navbar']['name'];
	foreach($model[$controller]['result']['records'] as $record) {
		if($record[$id_field] == $request_parameters['client_request']) {
			$view[$controller]['content'] = $record;
		}
	}

?>