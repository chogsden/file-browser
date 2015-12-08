<?PHP

	// Mongo DB Query generator:
	function mongoQuery($config, $request) {

		$result = false;
		$mongo_request = array();

		if(!isset($request['mode'])) {
			$request['mode'] = 'SELECT';
		}

		if(!isset($request['limit'])) {
			$request['limit'] = array(0,25);
		}

		if($GLOBALS['debug'] == true) {
			print_r($request);
		}
		createLog(prettyJson(json_encode($request)));

		// Execute Mongo query:
		if(isset($request)) {
			$result = mongoExecute($config, $request);
//			print_r($mongo_result);
		} else {
			$result = array(
				'query' => null,
				'result' => array(
					'records' => array(0 => null), 
					'response' => array(0 => array('error' => 'ERROR - incorrect array supplied'))
				)
			);
		}

		// Print query output to command line:
		if($GLOBALS['debug']) {
			echo	chr(10).
					'Mongo DB============================================================================================'.
					chr(10).$result['query'].chr(10).chr(10);
			print_r($result['result']); echo(chr(10).chr(10));
			echo 	'===================================================================================================='.
					chr(10);
		}

		return $result;
	}

	// Elasticsearch execute query procedure:
	function mongoExecute($config, $request) {
		$query = $request['mode'].'->'.$request['route'];

		try {
			$m = new Mongo($config['server']);
			$db = $m->$request['route']->collection;

			$result = false;

			switch($request['mode']) {
				case 'SELECT':
					try {
						if($db->find()) {
							foreach($db->find()->limit($request['limit'][1]) as $id => $record) {
								$id = (string)$record['_id'];
								unset($record['_id']);
								$result['records'][$id] = $record;
							}
//							print_r($result['records']);

						} else {
							$result['response']['error'] = 'Failed to get records from MongoDB';
						}
					} catch(MongoException $e) {
						$result['response']['error'] = $e->getMessage();
						return array('query' => $query, 'result' => $result);
					}
					break;
				case 'CREATE':
					if(!empty($request['items'])) {
						$counter = 0;
						foreach($request['items'] as $item_id => $item) {
							try {
								$item['_id'] = new MongoId();
								$id = (string)$item['_id'];
								if($db->insert($item)) {
									$result['records'][$item_id]['_id'] = $id;
									$result['response'][$item_id]['created'] = 1;
								} else {
									$result['response'][$item_id]['error'] = 'Failed to create record in MongoDB';
								}
							} catch(MongoException $e) {
								$result['response'][$item_id]['error'] = $e->getMessage();
								return array('query' => $query, 'result' => $result);
							} 
							$counter ++;
						}
					} else {
						$query = null;
						$result = array(
							'records' 	=> array(0 => null), 
							'response' 	=> array(0 => array('error' => 'ERROR - incorrect array supplied'))
						);
					}
					break;	
				case 'DELETE':
					if(!empty($request['items'])) {
						$counter = 0;
						foreach($request['items'] as $item_id => $item) {
							try {
								if($db->remove($item)) {
									$result['records'][$item_id] = $item;
									$result['response'][$item_id]['deleted'] = 1;
								} else {
									$result['response'][$item_id]['error'] = 'Failed to delete record in MongoDB';
								}
							} catch(MongoException $e) {
								$result['response'][$item_id]['error'] = $e->getMessage();
								return array('query' => $query, 'result' => $result);
							}
							$counter ++;
						}
					} else {
						$query = null;
						$result = array(
							'records' 	=> array(0 => null), 
							'response' 	=> array(0 => array('error' => 'ERROR - incorrect array supplied'))
						);
					}
					break;
			}
			$m->close();
			return array('query' => $query, 'result' => $result);

		} catch(MongoConnectionException $e) {
			// if there was an error, catch and display the problem here
        	return array(
        		'query' => $query, 
        		'result' => array(
        			'records' => array(0 => null), 
					'response' => array(0 => array('error' => 'ERROR: '.$e->getMessage()))
				)
			);
		}

	}

?>