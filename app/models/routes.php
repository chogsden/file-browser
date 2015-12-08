<?PHP 
	// MAA Model //
	
	// Set Config for data query parameters:
	$selectors = array(

		// ElasticSearch:
		'SEARCH_item' => array(

			'db'		=> 'es',
			'mode'		=> 'SELECT',
			'route'		=> 'file-browser',
			'filter'	=> array('term' => $model[$controller]['query']['filter'])
		),

		'SEARCH_INDEX_all' => array(

			'db'		=> 'es',
			'mode'		=> 'SELECT COUNT',
			'route'		=> 'file-browser/routes',
			'order'		=> array('order'),
			'return'	=> $model[$controller]['query']['return']
		),

		'CREATE_INDEX' => array(

			'db'	=> 'es',
			'mode'	=> 'CREATE',
			'route'	=> 'file-browser/routes',
			'items'	=> array($model[$controller]['query']['items'])
		),

		'DELETE_INDEX' => array(

			'db'	=> 'es',
			'mode'	=> 'DROP',
			'route'	=> 'file-browser/routes'
		),

		// MySQL:
		'SEARCH_all' => array(

			'db'	=> 'mysql',
			'mode'	=> 'SELECT COUNT',
			'return'=> $model[$controller]['query']['return']
		),

		'CREATE_TABLE' => array(

			'db'	=> 'mysql',
			'mode'	=> 'CREATE_TABLE',
			'fields'=> $model[$controller]['query']['fields']
		),

		'INSERT' => array(

			'db'	=> 'mysql',
			'mode'	=> 'INSERT',
			'items'	=> $model[$controller]['query']['items']
		),

		'DELETE_TABLE' => array(

			'db'	=> 'mysql',
			'mode'	=> 'DROP'
		),

			
	);

	// ------------------------------------------------------

	// Query DB and return to controller as an array:
	$model[$controller] = array_merge($model[$controller], dbRequest($selectors[$model[$controller]['selector']], $model[$controller]['query']['report']));

?>