<?PHP 

	// Function to return client device type:
	function clientDevice($mobile_agents, $user_agent) {
		$client_device = 'desktop';
		if(preg_match('/'.implode('|', $mobile_agents).'/', $user_agent)) {
			$client_device = 'mobile';
		}
		return $client_device;
	}

	// Function to set Application request parameters:
	function clientRequest($config) {

		global $routes;
		$GLOBALS['debug'] = false;

//		print_r($_SERVER);

		$routes['content'] = translateRoutes($config);
//		print_r($routes['content']);

		// Set application request parameters...

		// If access over http:
		if(empty($_SERVER['argv'])) {

			// Set default PHP Server request property if empty:
			if(!isset($_SERVER["REQUEST_URI"])) {
				$_SERVER["REQUEST_URI"] = '/'.$config['root_dir'].'home';
			}
			$GLOBALS['controller'] = 'application';
			$GLOBALS['debug'] = false;

		// if access from command line:
		} elseif(!empty($_SERVER['argv'])) {
			$_SERVER["HTTP_USER_AGENT"] = '';
			$config['domain_url'] = trim(preg_replace('@domain=@', '', $_SERVER['argv'][1]), '/').'/';
			$GLOBALS['controller'] = preg_replace('@controller=@', '', $_SERVER['argv'][2]);
			$request = trim(preg_replace('@request=@', '', $_SERVER['argv'][3]), '/');
			if(isset($_SERVER['argv'][4])) {
				$format = trim(preg_replace('@format=@', '', $_SERVER['argv'][4]), '/');
			} else {
				$format = '';
			}
			$_SERVER["REQUEST_URI"] = trim('commandline::/'.$config['root_dir'].$request, '/').'/'.$format;
			$GLOBALS['view_path'] = '';
			$GLOBALS['debug'] = true;
		}
		$GLOBALS['request_uri'] = $_SERVER["REQUEST_URI"];

		// Set application uri request parameters:
		$request_parameters = clientRequestValidation($config, $routes['content']);
		// If authentiacation is enabled, load AUTHENTICATION:
		if($config['authentication']['enable'] == true AND
			$GLOBALS['debug'] == false ) {
			authentication($config, $request_parameters);
		}

//		print_r($GLOBALS);
		return $request_parameters;

	}

	// Function to authenticate for secure access:
	function authentication($config, $request) {

		$authentication = true;

		// Allow restricted access to tunnel through AUTHENTICATION:
		preg_replace_callback(

			// THESE should be in Global Config file:
			array(
				'@^mediaserver.*@',
				'@^collections/item=.*.json@',
			),
			function($matches) use (&$authentication) {
//				print_r($matches);
				if(!empty($matches[0])) {
					$authentication = false;
				}
			},
			$request['this_url']
		);

		if($authentication == true) {
			if($request['client_request'] == 'login') {
			} elseif($request['client_request'] == 'logout') {
				setcookie($config['authentication']['cookie_name'], "", time()+3600,"/");
			} else {
				if(isset($_COOKIE[$config['authentication']['cookie_name']]) AND 
					preg_replace('@-.*@','',$_COOKIE[$config['authentication']['cookie_name']])==session_id()) {
					$cookie_name_arr = explode('-',$_COOKIE[$config['authentication']['cookie_name']]);
					$user = array(
						'id' => $cookie_name_arr[1],
						'username' => $cookie_name_arr[2]
					);
					setcookie($config['authentication']['cookie_name'], session_id().'-'.implode('-', $user), time()+3600,"/");
				} else {
					$GLOBALS['controller'] = 'authentication';
				}
			}
		}
	}

	function translateRoutes($config) {

		global $routes;
		
		$core_routes = json_decode(file_get_contents('config/routes.json'), true);
		if(!empty($config['routes'])) {
			require(loadMVC('controller', 'routes'));
			foreach($GLOBALS['routes']['model'] as $route) {
				$core_routes[$route['id']] = $route;
			}
		}
//		print_r($core_routes);
		return $core_routes;
	}

	// Function to validate client URL request parameters - used for controlling access and link navigation throughout the app:
	function clientRequestValidation($config, $routes) {
		
		$app_view = 'shared/_404';
		$route_request = '_null';
		$route_view = 'shared/_null';
		$route_name = '';
		$app_request = array();
		$output_format = 'html';

		$clean_uri = trim(preg_replace('@'.$config['root_dir'].'|commandline::@', '', $GLOBALS["request_uri"]), '/');
		$uri = explode('/',  $clean_uri);
//		print_r($uri);

		if(empty($uri[0])) {
			$client_request = 'home';
			$uri[0] = 'home';
		} else {
			$client_request = array_shift($uri);
		}

		$route_elements = $routes[$client_request];

		// Set parameter elements:
		$uri_elements = array();
		$app_elements = array(
			'section' => $client_request,
			'item' => '',
			'page' => '',
			'view' => '',
			'type' => '',
			'search' => ''
		);

		// Declare URL element rules for System use:
		$system_rules = array(
			'@(page)=([0-9]+)@', 
			'@(view)=(images|text|uploader|importer)@',
			'@('.implode('|', $config['allowed_output_formats']).')@'
		);

		// Build additional rules declared in global config:
		$additional_rules = array();
		foreach($config['url_validation_rules'] as $lookup => $rule) {
			$additional_rules[] = '@('.$lookup.')=('.$rule.')@';
		}
		$additional_rules[] = '@(item)=(.*)@';

		$url_rules = array_merge($system_rules, $additional_rules);
//		print_r($url_rules);

		// Validate URL elements and apply to parameters:
		preg_replace_callback(
			$url_rules,
			function($matches) use (&$uri_elements, &$app_elements, &$output_format) {
//				print_r($matches);
				switch($matches[1]) {
					case 'html':
					case 'json':
					case 'xml':
						$output_format = preg_replace('@\.@', '', $matches[1]);
						break;
					default:
						$uri_elements[$matches[1]] = preg_replace('@/.*@', '', $matches[2]);
						$app_elements[$matches[1]] = preg_replace('@/.*@', '', $matches[2]);
						break;
				}
			},
			$clean_uri
		);

		if(array_key_exists($client_request, $routes)) {
//			print_r($routes[$client_request]);
			$route_request = $routes[$client_request]['request'];
			if(!empty($routes[$client_request]['referer'])) {
				$route_request = $routes[$client_request]['referer'];
			}
			$app_request = array($route_request);
			$app_view = 'application';
			$route_view = $route_request;
			if(!empty($routes[$client_request]['navbar'])) {
				$route_name = $routes[$client_request]['navbar']['name'];
			}
//			print_r($uri);
			for($i=0; $i<count($uri); $i++) {
				if(	!empty($uri[$i]) AND
					isset($config['url_validation_rules']) AND
					preg_match('@'.implode('|', $config['url_validation_rules']).'@', $uri[$i]) == true) {
					if($output_format != 'html') {
						$app_view = $output_format;
						$route_view = 'shared/_null';
						$route_name = '';
						$output_format = $output_format;
					} else {
						$app_request[] = $uri[$i];
					}
				} else {
					$app_view = 'shared/_404';
					$route_request = '_null';
					$route_view = 'shared/_null';
					$route_name = '';
					break;
				}
			}
//			print_r($app_request);
		} else {
			$client_request = '_null';
		}
		return declareRequestParameters(
			$route_elements,
			$app_elements, 
			$GLOBALS["request_uri"], 
			$uri_elements,
			$config['domain_url'].$config['root_dir'].$clean_uri, 
			$config['domain_url'],
			$config['domain_url'].$config['root_dir'],
			$clean_uri.'/', 
			$client_request, 
			$route_request, 
			$route_view, 
			$route_name, 
			$output_format
		);

	}

	// Function to set core App declarations:
	function declareRequestParameters($route_elements, $app_elements, $base_uri, $uri_elements, $request_url, $domain_url, $base_url, $this_url, $client_request, $route_request, $route_view, $route_name, $output_format) {

		$request_parameters = array(
			// Route parameters:
			'route_elements' => $route_elements,

			// Application parameters:
			'app_elements' => $app_elements,

			// Request properties to application:
			'base_uri'	=>	$base_uri,

			// Client URI request elements:
			'uri_elements'	=>	$uri_elements,

			// Application view:
//			'app_view'		=>	$app_view,

			// Server request:
			'request_url'	=>	$request_url,

			// Server request:
			'domain_url'	=>	$domain_url,

			// Base URL request:
			'base_url'		=>	$base_url,

			// THIS request:
			'app_url'		=>	$this_url,

			// Client route request:
			'client_request'=>	$client_request,

			// Section route requested:
			'route_request'	=>	$route_request,

			// Section route view
			'route_view'	=>	$route_view,

			// Section route name
			'route_name'	=>	$route_name,

			// Format of application ouput:
			'output_format'	=>	$output_format,
		);
//		print_r($request_parameters);
		if($GLOBALS['debug'] == true) {
			print_r($request_parameters);
		}
		return $request_parameters;

	}

	// Function to call MVC module:
	// ============================
	// **** Exclusion for view path needs reworking! *****
	// ============================

	function loadMVC($type, $module) {

		$module = preg_replace('@_null/item@', '_null', $module);
//		echo ($module.chr(10));
		$source = $module;
		// Set global controller assertion:
		switch($type) {

			case 'controller':
				$GLOBALS['controller'] = $module;
				break;

			case 'model':
				$GLOBALS['model'][$GLOBALS['controller']]['model'] = $module;
				break;

			case 'view':
				if(!isset($GLOBALS['view'][$GLOBALS['controller']])) {
					$GLOBALS['view'][$GLOBALS['controller']] = array();
				}
				// Prepend $module with view folder:
				$source = $module.'/'.$module;
				// Ignore the following view paths:
				preg_replace_callback(
					'@(/item|shared|uploader|importer)@',
					function($matches) use (&$source, &$module) {
	//					print_r($matches);
						$source = preg_replace('@_null.*@', '_null', $module);
						unset($GLOBALS['view'][$module]);
					},
					$module
				);
				break;

		}
		$report = chr(10).'Loading '.$type.' '.$source.chr(10);

		// Output loaded module to screen:
		if($GLOBALS['debug'] == true) {
			echo($report);
		}
		createLog($report);
		return 'app/'.$type.'s/'.$source.'.php';
	}

	function echoContent($content) {
		$message[1] = date("Y-m-d H:i:s").(chr(10).
		'CONTENT============================================================================================='.
		chr(10))
		;
		$message[3] = (chr(10).
		'===================================================================================================='.
		chr(10))
		;
		if($GLOBALS['debug'] == true) {
			echo($message[1]);
			print_r($content);
			echo($message[3]);
		}

		$message[2] = preg_replace('@"|\\\@', '', prettyJson(json_encode($content)));
		ksort($message);
		createLog(implode('', $message));
	}

	function setModelParameters($query) {
		$criteria = array(
			'item_id', 
			'items', 
			'condition', 
			'filter', 
			'limit', 
			'order', 
			'mode', 
			'route', 
			'return', 
			'fields', 
			'report'
		);
		foreach($criteria as $clause) {
			if(!isset($query[$clause])) {
				$query[$clause] = false;
			}
		}
//		print_r($query);
		return $query;
	}

	function dbRequest($request, $report) {

		$dbs = array(
			'es' => 'elasticsearch',
			'mongo' => 'mongodb',
			'mysql' => 'mysql'
		);

		$message = array(chr(10).date("Y-m-d H:i:s"));
		$message[3] = '==============================================================================================';
		$message[4] = chr(10);

//		print_r($request);
		if(!empty($request['db']) AND isset($dbs[$request['db']])) {
			$db = $dbs[$request['db']];
			$db_config = yaml_parse_file('config/database.yml');
//			print_r($db_config);
			require_once('vendor/'.$db.'/functions.php');
			$db_result = call_user_func_array($request['db'].'Query', array($db_config[$db], $request, $report));

			if($report == true) {
				$message[1] = $dbs[$request['db']].'=================================================================================';
				$message[2] = preg_replace('@"|\\\@', '', prettyJson(json_encode($db_result['result'])));
			} else {
				$message = array();
			}

		} else {
			$message[2] = 'ERROR - no database criteria supplied';
		}

		ksort($message);
		createLog(implode(chr(10), $message));
		return($db_result);
	}

	function createLog($message) {
		require('config/global.php');
		if($config['development_log'] == true) {
			exec('echo "'.$message.'" >> '.$config['dev_log_path'].' 2>&1', $cl_output);
			if(!empty($cl_output)) {
				print_r($cl_output);
			}
		}
	}

	function deleteLog($config) {
		if($config['development_log'] == true) {
			exec('echo "" > '.$config['dev_log_path'].' 2>&1', $cl_output);
			if(!empty($cl_output)) {
	//			print_r($cl_output);
			}
		}
	}

	// Query START execution time:
	function getMicrotime() {
		$microtime_start = null;
		return (microtime(true) - $microtime_start);
	}

	// Query GET execution time - ( NOTE: doesn't work for CREATE TABLE qeries):
	function stopMicrotime($start_time) {
		return number_format((getMicrotime() - $start_time), 6) * 1000;
	}

	// Function to set text lookup rules for html markup:
	function setTextLookup($config_markup, $text_find, $text_replace) {		
		if(!empty($config_markup)) {
			$find = array();
			$replace = array();
			foreach($config_markup as $html_element => $css) {
				$text_find[] = '@<'.$html_element.'>@';
				$text_find[] = '@</'.$html_element.'>@';
				$text_replace[] = '<span class="'.$css.'">'; 
				$text_replace[] = '</span>';
			}
//			print_r($text_find);
//			print_r($text_replace);
		}
		return array('find' => $text_find, 'replace' => $text_replace);
	}

	// Function to markup HTML text in view:
	function htmlTextMarkup($lookup, $text) {
		return preg_replace($lookup['find'], $lookup['replace'], $text);
	}

	function navbar() {

		$routes = translateRoutes();
		
		$navbar_setup['home'] = '';
		// Get data from routes:
		foreach($routes as $id => $route) {
			if(isset($route['navbar'])) {
				$data = $route['navbar'];
				$url = '';
				if($route['request'] != 'home') {
					$url = $id;
				}
				
				// Set view navbar with a sngle page link:
				if($data['type'] == 'link') {
					
					// To set single link within a group of navbar links:
					if(!empty($data['group'])) {
						$navbar_setup[$data['group']]['type'] = 'list';
						$navbar_setup[$data['group']]['name'] = $data['group'];
						$navbar_setup[$data['group']]['items'][$id] = array('url' => $id, 'name' => $data['name']);
					} else {
						$navbar_setup[$id] = array('type' => 'link', 'url' => $url, 'name' => $data['name']);
					}

				// Set view navbar with a menu of sub-section links:
				} elseif($data['type'] == 'list') {
					$navbar_setup[$id] = array('type' => 'list', 'name' => $id, 'items' => array());
					foreach($data['list'] as $key => $list_item_name) {
						$navbar_setup[$id]['items'][$list_item_name] = array('url' => $id.'/item='.($key + 1).'/', 'name' => $list_item_name);
					}

				// Where the menu items are in a database table and accessd via a model:	
				} elseif($data['type'] == 'model') {	

					/* **** Needs recoding ****
						$data_filter = 0;
						require('app/models/'.$data['source']['model']['name'].'.php');
						foreach($model as $item_id => $item) {
							$navbar_setup[$id]['items'][] = array('url' => $route['request'].$item_id, 'name' => $item[$data['source']['model']['field']]);
						}
					*/
				}
			}
		}

		return $navbar_setup;

	}

		

	// Function to generate App section MVC files:
	function createMVC($routes, $section, $mysql) {
		require('_system/mvc.php');
		foreach($routes as $route) {
			$path = array();
			if($route == 'views') {
				if (!mkdir('app/'.$route.'/'.$section, 0755, true)) {
					$report = false;
				}
				$path['view1'] = 'app/'.$route.'/'.$section.'/'.$section;
				$path['view2'] = 'app/'.$route.'/'.$section.'/item';
				$mvc_code['view1'] = $mvc['view1'];
				$mvc_code['view2'] = $mvc['view2'];
			} else {
				$path[$route] = 'app/'.$route.'/'.$section;
				$version = 1;
				if($mysql == true) {
					$version = 2;
				}
				$mvc_code[$route] = $mvc[substr($route, 0, -1).$version];
			}
			foreach($path as $file_name => $file_path) {
				$file = fopen($path[$file_name].'.php',"w");
				if(fwrite($file, $mvc_code[$file_name])) {
					$report[substr($route, 0, -1)] = 'generated';
				} else {
					$report = false;
				}
				fclose($file);
			}
 		} 
 		return array('result' => array('response' => $report));
	}

	// Function to remove App section MVC files:
	function removeMVC($routes, $section) {
		foreach($routes as $route) {
			if($route == 'views') {
				$action = unlink('app/'.$route.'/'.$section.'/'.$section.'.php');
				$action = unlink('app/'.$route.'/'.$section.'/item.php');
				$action = rmdir('app/'.$route.'/'.$section);
			} else {
				$action = unlink('app/'.$route.'/'.$section.'.php');
			}
			if($action) {
				$report[substr($route, 0, -1)] = 'removed';
			} else {
				$report = false;
			}
		}
		return array('result' => array('response' => $report));
	}

	// Function to generate section in App Routes: 
	function updateRoutes($config, $app_paths, $routes, $section) {
		$file = fopen($app_paths['routes'], "w");
		$routes[$section] = array(
			'request'	=>	$section,
			'navbar'	=>	array(
				'name'	=>	ucwords(preg_replace('@_@', ' ', $section)),
				'type'	=>	'link',
				'group'	=>	''
			)
		);
		if(fwrite($file,prettyJson(json_encode($routes)))) {
			$report = 'routes.json: New section included';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

	// Function to remove section from App Routes: 
	function resetRoutes($app_paths, $routes, $section) {
		unset($routes[$section]);
		$file = fopen($app_paths['routes'], "w");
		if(fwrite($file,prettyJson(json_encode($routes)))) {
			$report = 'routes.json: Section removed';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

	// Function to output readable JSON:
	function prettyJson($json) {
 
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = chr(10);
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element, 
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine.$newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element, 
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

	function updateHTaccessRules($app_paths, $section, $action) {
		$file = fopen($app_paths['htaccess_rules'], 'r');
		$htaccess_rules = fread($file, 8192);
		fclose($file);
//		echo($htaccess_rules);
		if($action == 'include') {
			$update_rules = preg_replace('@(home)@', '$1|'.strtolower($section), $htaccess_rules);
		} elseif($action == 'remove') {
			$update_rules = preg_replace('@\|'.$section.'@', '', $htaccess_rules);
		} elseif($action == 'clear_all') {
			$update_rules = preg_replace('@(home).[a-z|0-9|_\||\s]+.(index.php)@', '$1 $2', $htaccess_rules);
		}
//		echo($update_rules);
		$file = fopen($app_paths['htaccess_rules'], 'w');
		if(fwrite($file, $update_rules)) {
			$report = '.htaccess rules: new section '.$action.'d';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

?>