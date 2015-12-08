<?PHP 

	// Elasticsearch Query generator:
	function esQuery($config, $request, $report) {

		$query = array();
		$method = '';
		$url = '';
		$result = false;
		$es_query = array();

		if(!isset($request['mode'])) {
			$request['mode'] = 'SELECT';
		}
		/*
		if(strstr($request['mode'], 'SELECT') == true) {
			if(!isset($request['order'])) {
				$request['order'] = array('id' => 'asc');
			}
		}
		*/
		if($GLOBALS['debug'] == true) {
			print_r($request);
		}
		createLog(prettyJson(json_encode($request)));

		$query_build = $request;
		unset($query_build['db'], $query_build['route'], $query_build['mode'], $query_build['items']);

		foreach($query_build as $function => $arg) {
			if(!empty($arg)) {
				$es_query = call_user_func_array('es'.ucfirst($function), array($es_query, $arg));
			}
		}
//		print_r($es_query);

		switch($request['mode']) {

			case 'SELECT':
			case 'SELECT COUNT':
				$method = 'POST';
				$query = array($es_query);
				$url = $config['server'].$request['route'].'/_search?';
				break;
		
			case 'UPDATE':
				break;

			case 'CREATE':
				$method = 'POST';
				$query = $request['items'];
				$url = $config['server'].$request['route'];
//				createLog(json_encode($request));
				break;

			case 'DELETE':
				$method = 'DELETE';
				$query = $request['items'];
				$url = $config['server'].$request['route'];
				break;

			case 'DROP':
				$method = 'DELETE';
				$query = array(true);
				$url = $config['server'].$request['route'];
				break;
		}

		if(!empty($query)) {
			$es_result = esExecute(
				$request['mode'], 
				$method, 
				$url, 
				$query
			);
//			print_r($es_result);
		} else {
			$es_result = array(
				'query' => null,
				'result' => array(
					'records' => array(0 => null), 
					'response' => array(0 => array('error' => 'ERROR - query is null'))
				)
			);
		}

		// Print query output to command line:
		if($GLOBALS['debug'] AND $report == true) {
			echo	chr(10).
					'ElasticSearch=======================================================================================';
//			echo	chr(10).$es_result['query'].chr(10).chr(10);
			print_r($es_result['result']);
			echo 	'===================================================================================================='.
					chr(10);
		}

		return array('result' => $es_result['result'], 'query' => $query);
	}

	// Elasticsearch execute query procedure:
	function esExecute($mode, $method, $url, $query) {

		$result = array('records' => array());
		$time = 0;

		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
		);
		
		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // return response as string

		foreach($query as $num => $query_string) {
//			print_r($query_string);
			switch($method) {
				case 'GET':
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
					break;

				case 'POST':
					try {
						$json = json_encode($query_string);
						$echo_query[$num] = preg_replace('@\t@', '', prettyJson($json));
//						echo($echo_query[$num]);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
						curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					} catch( ElasticsearchParseException $e) {
					}
					break;

				case 'DELETE':
					$echo_query[$num] = $url.'/'.$query_string['_id'];
					curl_setopt($ch, CURLOPT_URL, $url.'/'.$query_string['_id']);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;

				case 'DROP':
					$echo_query[$num] = $url;
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}
			$es_results = json_decode(curl_exec($ch), true);
//			print_r($es_results);

			if(!isset($es_results['error'])) {

				switch($mode) {
					case 'SELECT':
					case 'SELECT COUNT':
//						echo($es_results['hits']['total']);
						foreach($es_results['hits']['hits'] as $row) {
							$result['records'][$row['_id']] = $row['_source'];
							$result['records'][$row['_id']]['_type'] = $row['_type'];
						}
						$result['response'] = count($es_results['hits']['hits']).' record(s) retrieved';
						if($mode == 'SELECT COUNT') {
							$result['record_count'] = $es_results['hits']['total'];
						}
						$time = $es_results['took'];
						break;

					case 'CREATE':
//						$echo_query = '';
						$result['records'][$num]['_id'] = $es_results['_id'];
						$result['response'][$num] = $es_results;
						break;

					case 'DELETE':
						$echo_query = '';
						$result['records'][$num]['_id'] = $query_string['_id'];
						$result['response'][$num] = $es_results;
						break;

					case 'DROP':
						$echo_query = '';
						$result['response'][$num] = $es_results;
						break;
				}

			} else {
				$result['response'][$num] = $es_results;
				$result['record_count'] = 0;
	//			die();
			}
		}
		return array('query' => $echo_query, 'result' => $result, 'execcution_time' => $time);
		curl_close($ch);
	}

	// Elasticsearch query WHERE statement:
	function esBool($es_query, $request) {

		/* Needs Developing as part of Search interface

			Using query and wildcard, or filter and regexp filter 

		*/

		foreach($request as $field => $value) {
			$es_query['query']['filtered']['query']['bool']['should']['wildcard'][$field] = array('value' => $value);
		}
		return $es_query;
	}

	// Elasticsearch query WHERE statement:
	function esFilter($es_query, $request) {
		foreach($request as $type => $filter) {
			foreach($filter as $arg1 => $arg2) {
				if($arg1 == '_id') {
					$es_query['query'][$type][$arg1] = $arg2;
				} else {
					$es_query['query']['filtered']['filter'][$type][$arg1] = $arg2;
				}
			}
		}
		return $es_query;
	}

	// Elasticsearch query WHERE statement:
	function esCondition($es_query, $request) {
		$query_build = array();
//		print_r($request);
		foreach($request as $type => $query) {
			switch($type) {
				case 'match':
					foreach($query as $field => $value) {
						$query_build['must'] = array('match' => array($field => array('query' => $value, 'type' => 'phrase')));
					}
					break;

				case 'condition':
					foreach($query as $condition) {
						foreach($condition as $field => $value) {
							$query_build['should'][] = array('term' => array($field => $value));
						}
					}
					break;
			}
//			print_r($query);	
        }
        $query_build['minimum_should_match'] = 1;
		$es_query['query']['filtered']['query']['bool'] = $query_build;
		return $es_query;
	}

	// Elasticsearch query FIELDS statement:
	function esReturn($es_query, $request) {
		array_unshift($request, 'id');
		$es_query['_source'] = $request;
		return $es_query;
	}

	// Elasticsearch query LIMIT statement:
	function esLimit($es_query, $request) {
		$es_query['from'] = $request[0];
		$es_query['size'] = $request[1];
		return $es_query;
	}

	// Elasticsearch query ORDER BY statement:
	function esOrder($es_query, $request) {
		foreach($request as $field => $order) {
			if(empty($order)) {
				$order = 'asc';
			}
			$es_query['sort'][$field] = $order;
		}
		return $es_query;
	}

?>