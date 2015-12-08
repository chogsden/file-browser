<?PHP 

	// Function to query MySQL database and return result:
	function mysqlQuery($config, $request, $report) {
		$mysql = mysqlAccess($config);
		$start_time = getMicrotime();
//		$show_time = true;

		if(!isset($request['mode'])) {
			$request['mode'] = 'SELECT';
		}
		$request['model'] = $GLOBALS['model'][$GLOBALS['controller']]['model'];
		if($GLOBALS['debug'] == true) {
			print_r($request);
		}

		$db_result = buildQuery($mysql, $request, $start_time, $report);

		// Return MySQL query result and query:
		return array('result' => $db_result['result'], 'query' => $db_result['query']);
		
		// Close MySQL connection:
		$mysql->close();
	}

	// Function to access MySQL database:
	function mysqlAccess($config) {
		$mysql = new mysqli($config['server'], $config['user'], $config['pass'], $config['db']);
		// Check for MySQL connection: 
		if ($mysql->connect_error) {
		    printf("Connect to MySQL database failed: %s\n", mysqli_connect_error());
		    exit();
		}
		$mysql->query('SET NAMES utf8');
		return($mysql);
	}

	// MySQL Query generator:
	function buildQuery($mysql, $request, $start_time, $report) {

		$query = array();
		$result = false;
		$response = '';

		switch($request['mode']) {

			case 'SELECT':
			case 'SELECT COUNT':
				$query = array(
					 'SELECT DISTINCT SQL_CALC_FOUND_ROWS '.sqlReturn($request).' FROM '.sqlRoute($request),
					sqlCondition($request),
					sqlGroupBy($request),
					sqlOrder($request).' '.sqlLimit($request)
				);
				break;
		
			case 'INSERT':
				$query = array(
					'INSERT INTO '.sqlRoute($request),
					sqlInsertFields($request),
					sqlCondition($request)
				);
				break;

			case 'CREATE_TABLE':
				$query = array(
					'CREATE TABLE `'.$request['model'].'`',
					sqlCreateFields($request),
					'ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
				);
				$response = 'created table';
				break;

			case 'DELETE':
				$query = array(
					'DELETE FROM `'.$request['model'].'`',
					sqlCondition($request)
				);
				$response = 'record(s) deleted';
				break;

			case 'DROP':
				$query = array(
					'DROP TABLE `'.$request['model'].'`'
				);
				$response = 'table removed';
				break;
		}

		$query = preg_replace('@'.chr(9).'@', '', implode(' ', $query));
		$mysqlresult = mysqlExecute($request, $mysql, $query, $start_time, $response);
//		print_r($mysqlresult);

		// Print query output to command line:
		if($GLOBALS['debug'] AND $report == true) {
			echo	chr(10).
					'MySQL=======================================================================================';
			echo	chr(10).$query.chr(10).chr(10);
			print_r($mysqlresult['result']);
			echo 	'============================================================================================'.
					chr(10);
		}
		return array('result' => $mysqlresult['result'], 'query' => $query);
	}

	// MySQL execute query procedure:
	function mysqlExecute($request, $mysql, $query, $start_time, $response) {
		$result = false;
//		echo(chr(10).$query.chr(10));
//		$query = $mysql->real_escape_string($query);
		if($mysqlresult = $mysql->query($query)) {
			$time = stopMicrotime($start_time);

			switch($request['mode']) {
				case 'SELECT':
				case 'SELECT COUNT':
					$result['records'] = array();
					if($mysqlresult->num_rows == true) {
						while($row = $mysqlresult->fetch_array(MYSQLI_ASSOC)) {
							$result['records'][$row['id']] = $row;
						}
					}
					$response = $mysqlresult->num_rows.' record(s) retrieved';

					if($request['mode'] == 'SELECT COUNT') {
						$count_query = 'SELECT FOUND_ROWS() as record_count';
//						echo(chr(10).$count_query.chr(10));
						$countresult = $mysql->query($count_query);
						while($row = $countresult->fetch_array(MYSQLI_ASSOC)) {
							$result['record_count'] = $row['record_count'];
						}
					}
					$mysqlresult->close();
					break;

				case 'INSERT':
					$response = array(
						'id' => $mysql->insert_id,
						'report' => 'created record with id = '.$mysql->insert_id
					);
					break;
			}
			$result['response'] = $response;
//			print_r($result);
			return array('result' => $result, 'execution_time' => $time);
		
		} else {
			echo(chr(10).'MySQL ERROR - '.$mysql->error.chr(10).chr(10));
//			die();
			return array('result' => false, 'execcution_time' => 0);
		}
	}

	// SQL query WHERE statement:
	function sqlCondition($request) {
		$sql_condition = '';
		if(isset($request['condition'])) {
			$sql_condition = 'WHERE '.implode(' AND ', $request['condition']);
		}
		return $sql_condition;
	}

	// SQL query TABLE and JOIN statement:
	function sqlRoute($request) {
		$sql_route = $request['model'];
		if(isset($request['route'])) {
			$join_statement = array(0 => $request['model']);
			foreach($request['route'] as $rules) {
				$join[3] = 'ON';
				if(isset($rules['belongs_to'])) {
					$join[1] = 'JOIN';
					$join[2] = $rules['belongs_to'];
					$join[4] = $rules['using'].' = '.$rules['belongs_to'].'.id';
				} elseif(isset($rules['has_many'])) {
					$join[1] = 'LEFT JOIN';
					$join[2] = $rules['has_many'];
					$join[4] = $request['model'].'.id = '.$rules['using'];
				}
				ksort($join);
				if(isset($rules['condition'])) {
					array_splice($join, 3, 0, '(');
					$join[] = 'AND '.$rules['condition'].' )';
				}
//				print_r($join);
				$join_statement[] = implode(' ', $join);
			}
			$sql_route = implode(' ', $join_statement);
		}
//		echo($sql_route);
		return $sql_route;
	}

	// SQL query FIELDS statement:
	function sqlReturn($request) {
		$sql_return = $request['model'].'.*';
		if(isset($request['return'])) {
			$return = array($request['model'].'.id');
			foreach($request['return'] as $field => $new_field) {
				if(!empty($new_field)) {
					$field = $field.' AS '.$new_field;
				}
				$return[] = $field;
			}
			$sql_return = implode(', ', $return);
		}
		return $sql_return;
	}

	// SQL query LIMIT statement:
	function sqlLimit($request) {
		$sql_limit = '';
		if(isset($request['limit'])) {
			$sql_limit = 'LIMIT '.implode(',', $request['limit']);
		}
		return $sql_limit;
	}

	// SQL query ORDER BY statement:
	function sqlOrder($request) {
		if(!isset($request['order'])) {
			$request['order'] = array($request['model'].'.id' => '');
		}
		$order_array = array();
		foreach($request['order'] as $field => $order) {
			if(is_int($field)) {
				$field = $order;
				$order = 'asc';
			}
			$order_array[] = $field.' '.$order;
		}
		$sql_order = 'ORDER BY '.implode(',', $order_array);
		return $sql_order;
	}

	// SQL query LIMIT statement:
	function sqlGroupBy($request) {
		$sql_groupby = '';
		if(isset($request['groupBy'])) {
			$sql_groupby = 'GROUP BY '.implode(',', $request['groupBy']);
		}
		return $sql_groupby;
	}

	// SQL query CREATE FIELDS statement:
	function sqlCreateFields($request) {
		$sql_create_fields = array(
			'`id` int(11) unsigned NOT NULL AUTO_INCREMENT',
			'`created_at` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
			'`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		);
		if(isset($request['fields'])) {
			$field_structure = array();
			foreach($request['fields'] as $field => $value) {
				$field_structure[$field] = '`'.$field.'` '.$value;
			}
			array_splice($sql_create_fields, 1, 0, implode(', ', $field_structure));
		}
		$sql_create_fields[] = 'PRIMARY KEY (`id`)';
		return '('.implode(', ',$sql_create_fields).')';
	}

	// SQL query INSERT FIELDS statement:
	function sqlInsertFields($request) {
		$insert_fields = array('id');
		$insert_data = array('null');
		$find = array("@'@",'@"@');
		$replace = array("\'",'\"');
		if(isset($request['items'])) {
			foreach($request['items'] as $field => $data) {
				$insert_fields[] = $field;
				$insert_data[] = '"'.preg_replace($find, $replace, $data).'"';
			//	$insert_data[] = preg_replace($find, $replace, $data);
			//	$insert_data[] = '"'.$data.'"';
			}
		}
		return '('.implode(', ',$insert_fields).', created_at) VALUES('.implode(', ', $insert_data).', "'.date("Y-m-d H:i:s").'")';
	}
	
?>