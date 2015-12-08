
<?PHP
	// ROUTES Controller //

	$controller = $GLOBALS['controller'];
	$database = 'es';
//	$database = 'mysql';

	// Set Model query parameters:

		if($database == 'es') {

			// Set Model query type:
			$model[$controller]['selector'] = 'SEARCH_INDEX_all';

			$criteria['return'] = array('id', 'request', 'navbar', 'settings');

		} elseif($database == 'mysql') {

			// Set Model query type:
			$model[$controller]['selector'] = 'SEARCH_all';

			$criteria['return'] = array('json' => 'json');

		}

		$model[$controller]['criteria'] = array(

			// Send report to commandline:
			'report' => false,

			// Set fields to return from model:
			'return' => $criteria['return']

		);

		// Get data from the ROUTES model:
		$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
		require(loadMVC('model', 'routes'));

		if($database == 'es') {

			$routes['model'] = $model[$controller]['result']['records'];

		} elseif($database == 'mysql') {

			foreach(json_decode($model[$controller]['result']['records'][1]['json'], true) as $route) {
					foreach($route as $id => $record) {
						$routes['model'][$record['settings']['order']] = $record;
					}
			}
			ksort($routes['model']);
		}

?>