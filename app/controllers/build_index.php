
<?PHP
	// BUILD INDEX Controller //

	$view_path = 'shared/_null';

	$database = 'es';
//	$database = 'mysql';

	$content_directory = 'file-directory/';
	$directory_array = explode('/', $content_directory);
	$path = realpath($config['root_path'].'/'.$content_directory);
	$dir_array = array();
	$content_array = array();
	$content_links = array();

	$objects = new RecursiveIteratorIterator (new recursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
	
	function make_nested($array, $content) {
//		print_r($array);
		$content['level'] ++;
	    if(count($array) < 2) {
//	    	print_r($array);
	    	foreach($array as $key => $name) {
	    		return array($name => $content);
	    	}
	    }
	    $key = array_shift($array);
	    return array($key => make_nested($array, $content));
	}

	function buildContentArray($content_array, $content) {
		$content_array[] = $content;
		return $content_array;
	}

	$count = 1;
	$section = '';
	$parent = '';
	$mapping = array(
				'yml' => 'settings',
		    	'jpg' => 'image',
		    	'txt' => 'text',
		    	'lnk' => null
		    );
	foreach($objects as $name => $object){
//		echo "$name ".chr(10);
		$info = pathinfo($object);
		$filename = $info['filename'];
//		print_r($info);
		if(!isset($info['filename']) OR $info['filename'] == '' OR $info['filename'] == '.') {
		} else {
			$arr = explode('/', $name);
//			$filename = preg_replace('@\.@', '_', $info['filename']);
 
			if(strstr($name, '_content') == true) {
				if(isset($info['extension']) AND $info['extension'] != 'yml') {

					$content = array('id' => $id, 'parent' => $parent, 'name' => $filename, 'type' => $mapping[$info['extension']], 'section_id' => $section_id, 'order_by' => $count);
				    switch ($info['extension']) {

				    	case 'jpg':
				    		$content['path'] = $path;
				    		$content['parent'] = $parent;
				    		$content['content'] = 'url';
				    		$content_array = buildContentArray($content_array, $content);
				    		break;

				    	case 'txt':
				    		$content['path'] = $path;
				    		$content['parent'] = $parent;
				    		$content['content'] = file_get_contents($name);
				    		$content_array = buildContentArray($content_array, $content);
				    		break;

				    	case 'lnk':
				    		$content_links[$id] = array(
				    			'links' => explode(chr(10), file_get_contents($name)),
				    			'elements' => $content
				    		);
				    }
				    $count ++;
				} else {
					$parent = $filename;
					$id = preg_replace('@/_content@', '', $section_id.'/'.$parent);
					$path = preg_replace(array('@'.$config['root_path'].'@', '@'.$filename.'@'), array($request_parameters['domain_url']. ''), $name);
				}
		    } elseif(strstr($name, '_settings') == true) {

		    } else {
	    		$section_id = preg_replace(array('@ @'), array('_'), $filename);
		    	$structure = array(
		    		'type' => 'directory',
		    		'id' => $section_id,
		    		'level' => 0,
		    		'order' => $count
		    	);
//		    	print_r(make_nested($arr, $content));
		    	$dir_array = array_merge_recursive(make_nested(array_splice($arr, 4), $structure), $dir_array);
		    }
		}
	}

	print_r($content_links);
	foreach($content_links as $id => $link_info) {
		foreach($link_info['links'] as $link) {
			$linked_content = array_keys(array_column($content_array, 'id'), $link);
			print_r($linked_content);
			foreach($linked_content as $content_id) {
				$link_source = $content_array[$content_id];
				$link_source['id'] = preg_replace('@'.$link_source['section_id'].'@', $link_info['elements']['section_id'], $link_source['id']);
				$link_source['section_id'] = $link_info['elements']['section_id'];
				$link_source['order_by'] = $count;
				$content_array[] = $link_source;
				$count ++;
			}
		}
	}

	print_r($dir_array);
	print_r($content_array);

	if($database == 'es') {

		// DROP ROUTES index:

			// Set Model query criteria:
				$model[$controller]['selector'] = 'DELETE_INDEX';
				$model[$controller]['criteria'] = array('report' => true);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'routes'));

			// Remove routes from .htaccess file:
			updateHTaccessRules(array('htaccess_rules'=>'.htaccess'), null, 'clear_all');

		// DROP CONTENTS index:

			// Set Model query criteria:
				$model[$controller]['selector'] = 'DELETE_INDEX';
				$model[$controller]['criteria'] = array('report' => true);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'content'));


		// INSERT routes json into ROUTES table:

			function model($content_directory, $parent, $name, $info) {
				
				$view_config = yaml_parse_file($content_directory.$name.'/_settings/settings.yml');
				print_r($view_config);

				$id = preg_replace('@ @', '_', $name);

				$controller = $GLOBALS['controller'];

				// Set model parameters:
					$model[$controller]['selector'] = 'CREATE_INDEX';
					$model[$controller]['criteria'] = array(
						'items' => array(
							'id' => $id,
							'request' => 'content',
							'navbar' => array(
								'name' => ucfirst($name),
								'type' => 'link',
								'group' => ucfirst($parent),
							),
							'settings' => $view_config,
							'order' => $view_config['order']),
						'report' => true
					);

				// Get response from the ROUTES model:
				$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
				require(loadMVC('model', 'routes'));

				// Insert routes into .htaccess file:
				updateHTaccessRules(array('htaccess_rules'=>'.htaccess'), $id, 'include');

			}

			foreach($dir_array['file-directory'] as $dir => $dir_info) {
				$sub_menu = false;
				foreach($dir_info as $key => $value) {
					if(is_array($value)) {
						model($config['root_path'].$content_directory.$dir.'/', $dir, $key, $value);
						$sub_menu = true;
					}
				}
				if($sub_menu == false) {
					model($config['root_path'].$content_directory, '', $dir, $dir_info);
					
				}
			}

		foreach($content_array as $id => $content) {
			// Set Model query criteria:
				$model[$controller]['selector'] = 'CREATE_INDEX';
				$model[$controller]['criteria'] = array(
					'items' => array($content),
					'report' => true
				);

			// Get response from the CONTENT model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'content'));

		}

	} elseif($database == 'mysql') {

		// DROP ROUTES table:

			// Set Model query criteria:
				$model[$controller]['selector'] = 'DELETE_TABLE';
				$model[$controller]['criteria'] = array('report' => true);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'routes'));

			// Remove routes from .htaccess file:
			updateHTaccessRules(array('htaccess_rules'=>'.htaccess'), null, 'clear_all');

		// DROP CONTENTS table:

			// Set Model query criteria:
				$model[$controller]['selector'] = 'DELETE_TABLE';
				$model[$controller]['criteria'] = array('report' => true);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'content'));

		// CREATE ROUTES table

			// Set Model query criteria:
				$model[$controller]['selector'] = 'CREATE_TABLE';
				$model[$controller]['criteria'] = array(
					'fields'=> array(
						'json' => 'TEXT NOT NULL'),
					'report' => true
				);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'routes'));

		// CREATE CONTENT table

			// Set Model query criteria:
				$model[$controller]['selector'] = 'CREATE_TABLE';
				$model[$controller]['criteria'] = array(
					'fields'=> array(
						'content_id'	=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'parent' 		=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'name' 			=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'type' 			=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'section_id' 	=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'order_by' 		=> 'INT NOT NULL DEFAULT \'0\'',
	                    'path' 			=> 'varchar(255) NOT NULL DEFAULT \'\'',
	                    'content' 		=> 'TEXT'),
					'report' => true
				);

			// Get response from the ROUTES model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'content'));


		// INSERT routes json into ROUTES table:

			function model($content_directory, $parent, $name, $info) {
				
				$view_config = yaml_parse_file($content_directory.$name.'/_settings/settings.yml');
	//			print_r($view_config);

				$id = preg_replace('@ @', '_', $name);

				$criteria[$info['id']] = array(
					'id' => $id,
					'request' => 'content',
					'navbar' => array(
						'name' => ucfirst($name),
						'type' => 'link',
						'group' => ucfirst($parent),
					),
					'settings' => $view_config,
	//				'order' => $info['order']
				);

				// Insert routes into .htaccess file:
				updateHTaccessRules(array('htaccess_rules'=>'.htaccess'), $id, 'include');

				return $criteria;

			}

			$criteria = array();
			foreach($dir_array['file-directory'] as $dir => $dir_info) {
				$sub_menu = false;
				foreach($dir_info as $key => $value) {
					if(is_array($value)) {
						$criteria[] = model($config['root_path'].$content_directory.$dir.'/', $dir, $key, $value);
						$sub_menu = true;
					}
				}
				if($sub_menu == false) {
					$criteria[] = model($config['root_path'].$content_directory, '', $dir, $dir_info);
					
				}
			}

			// Set Model query criteria:
				$model[$controller]['selector'] = 'INSERT';
				$model[$controller]['criteria'] = array(
				'items'=> array(
					'json' => json_encode($criteria)),
					'report' => true
				);

			// Get response from the SECTION model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
			require(loadMVC('model', 'routes'));	

		// INSERT content into CONTENT table:

			foreach($content_array as $id => $content) {

				$content['content_id'] = $content['id'];
				unset($content['id']);
				// Set Model query criteria:
					$model[$controller]['selector'] = 'INSERT';
					$model[$controller]['criteria'] = array(
						'items'=> $content,
						'report' => true
					);

				// Get response from the SECTION model:
				$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
				require(loadMVC('model', 'content'));

			}



	}

?>