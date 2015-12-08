<?PHP
	// TEMPLATE 2 Controller //
	
	if(!empty($request_parameters['app_elements']['item'])) {

		// Re-route view to item view:
		$view_path .= '/item';

		// Set Model query parameters:

			if($database == 'es') {

				// Set Model query type:
				$model[$controller]['selector'] = 'SEARCH_INDEX_item';

				$criteria['condition'] = array('id' => $request_parameters['client_request'].'/'.$request_parameters['app_elements']['item']);
			
			} elseif($database == 'mysql') {

				// Set Model query type:
				$model[$controller]['selector'] = 'SEARCH_item';

				$criteria['condition'] = array('content_id = "'. $request_parameters['client_request'].'/'.$request_parameters['app_elements']['item'].'"');
			}

			// Set Model query elements:

			$model[$controller]['criteria'] = array(

				// Set query condition:
				'condition' => $criteria['condition'],
				
				// Set order of returned records:
				'order' => array('order_by'),

				// Send report to commandline:
				'report' => true

			);

		// Get data from the SECTION model:
		$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
		require(loadMVC('model', 'content'));

		if(!empty($model[$controller]['result']['records'])) {
			foreach ($model[$controller]['result']['records'] as $id => $record) {
				$view[$controller]['content']['items'][] = $record;
			}
			$view[$controller]['title'] = $view[$controller]['content']['items'][0]['parent'];
		}

	} else {

		// LOAD CONTENT FOR SEARCH PAGE:

		// Set page requested:
		require(loadMVC('helper', 'pages'));

		// Set number of items to show per page:
		$pages['items_per_page'] = 99;

		// Set Model query parameters:

			if($database == 'es') {

				// Set Model query type:
				$model[$controller]['selector'] = 'SEARCH_INDEX_all';

				$criteria['filter']	= array('section_id' => $request_parameters['client_request']);

				$criteria['condition'] = array(array('type' => 'image'));
			
			} elseif($database == 'mysql') {

				// Set Model query type:
				$model[$controller]['selector'] = 'SEARCH_all';

				$criteria['filter'] = array('section_id = "'.$request_parameters['client_request'].'"', 'type = "image"');

				$criteria['condition'] = null;
			}

			// Set Model query elements:

			$model[$controller]['criteria'] = array(

				// Set record return limit criteria for db query:
				'limit'		=> array($pages['request'] * $pages['items_per_page'], $pages['items_per_page']),

				'filter'	=> $criteria['filter'],

				'condition' => $criteria['condition'],

				'order'		=> array('parent', 'order_by'),

				// Set fields to return from model:
				'return'	=> array('name', 'type', 'content'),

				// Send report to commandline:
				'report'	=> true

			);

		// Get data from the SECTION model:
		$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
		require(loadMVC('model', 'content'));

		// BUILD CONTENT FOR VIEW:
		$view[$controller]['title'] = $request_parameters['route_elements']['navbar']['name'];
		$view[$controller]['content']['items'] = $model[$controller]['result']['records'];

		// Build page navigation criteria for view:
		require(loadMVC('helper', 'page_nav'));
	}


?>