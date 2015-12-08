<?PHP 
	// AUTHENTICATION Model //
	/*
		// Set Config for data query parameters:
		$selectors = array(

			'SEARCH_item' => array(

			),

			'SEARCH_all' => array(

			)
				
		);

		// ------------------------------------------------------

		// Query DB and return to controller as an array:
		$model = dbRequest($selectors[$model_params['selector']], $model_params['criteria']['report']);
	*/

	$users = array(
		1 => array(
			'username'	=>	'ucm-publisher',
			'password'	=>	'$1$Q94iAt8D$ZFrg5tGtVquWwAtpxTMfU.'
		)
	);
	$model = array();
	foreach($users as $user_id => $user) {
		if($user['username'] == $criteria['filter']['username']) {
			$model['result']['records'][$user_id] = $user; 
		}
	}

?>