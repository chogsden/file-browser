<?PHP

	// NAVBAR Helper //

	// Include home image/logo in navbar:
	$content['nav_bar_logo']['image'] = '';
	$content['nav_bar_logo']['url_link'] = $request_parameters['base_url'];
	$content['nav_bar_logo']['link_target'] = 'self';

	// Set navbar menu items:

	$content['navbar']['home'] = '';

	// Get data from routes:
	foreach($routes['content'] as $id => $route) {
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
					$content['navbar'][$data['group']]['type'] = 'list';
					$content['navbar'][$data['group']]['name'] = $data['group'];
					$content['navbar'][$data['group']]['items'][$id] = array('url' => $id, 'name' => $data['name']);
				} else {
					$content['navbar'][$id] = array('type' => 'link', 'url' => $url, 'name' => $data['name']);
				}

			// Set view navbar with a menu of sub-section links:
			} elseif($data['type'] == 'list') {
				$list_html = array();
				$content['navbar'][$id] = array('type' => 'list', 'name' => $id, 'items' => array());

				// Where the menu items are listed in app/core/routes.json:
				if($data['source']['type'] == 'list') {
					foreach($data['source']['items'] as $item_id => $item) {
						$content['navbar'][$id]['items'][$item_id] = array('url' => $id.$item_id, 'name' => $item['name']);
					}

				// Where the menu items are in a database table and accessd via a model:	
				} elseif($data['source']['type'] == 'model') {	

				/* **** Needs recoding ****
					$data_filter = 0;
					require('app/models/'.$data['source']['model']['name'].'.php');
					foreach($model as $item_id => $item) {
						$content['navbar'][$id]['items'][] = array('url' => $route['request'].$item_id, 'name' => $item[$data['source']['model']['field']]);
					}
				*/
				}
			}
		}
	}
//	print_r($content['navbar']);

?>