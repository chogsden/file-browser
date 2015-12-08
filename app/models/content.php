<?PHP 
	// MAA Model //
	
	// Set Config for data query parameters:
	$selectors = array(

		// ElasticSearch:
		'SEARCH_INDEX_item' => array(

			'db'		=> 'es',
			'mode'		=> 'SELECT',
			'route'		=> 'file-browser/content',
			'condition' => array('match' => $model[$controller]['query']['condition']),
			'order'		=> $model[$controller]['query']['order'],
		),

		'SEARCH_INDEX_all' => array(

			'db'		=> 'es',
			'mode'		=> 'SELECT COUNT',
			'route'		=> 'file-browser/content',
			'limit'		=> $model[$controller]['query']['limit'],
			'filter'	=> array('term' => $model[$controller]['query']['filter']),
			'condition'	=> $model[$controller]['query']['condition'],
			'order'		=> $model[$controller]['query']['order'],
//			'return'	=> $model_params['query']['return']
		),

		'CREATE_INDEX' => array(

			'db'	=> 'es',
			'mode'	=> 'CREATE',
			'route'	=> 'file-browser/content',
			'items'	=> $model[$controller]['query']['items']
		),

		'DELETE_INDEX' => array(

			'db'	=> 'es',
			'mode'	=> 'DROP',
			'route'	=> 'file-browser/content'
		),

		// MySQL:
		'SEARCH_item' => array(

			'db'		=> 'mysql',
			'mode'		=> 'SELECT',
			'condition' => $model[$controller]['query']['condition'],
			'order'		=> $model[$controller]['query']['order'],
		),

		'SEARCH_all' => array(

			'db'		=> 'mysql',
			'mode'		=> 'SELECT COUNT',
			'limit'		=> $model[$controller]['query']['limit'],
			'condition'	=> $model[$controller]['query']['filter'],
			'order'		=> $model[$controller]['query']['order'],
//			'return'	=> $model_params['query']['return']
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