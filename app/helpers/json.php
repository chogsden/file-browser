<?PHP

	// JSON Controller:
/*
	$json_array = array(
		'uri' => $request_parameters['request_uri']
	);		

	if($request_parameters['route_request'] == 'collections') {
		$key = 0;
		foreach($content['view']['items'] as $source => $records) {
			foreach($records as $record_id => $items) {
				$items = array(
					'id' => array('_id', $record_id),
					'type' => array('_type', $source)
				) + $items;
				foreach($items as $item) {
					if(isset($content['view']['records'][$key][$item[0]])) {
						$content['view']['records'][$key][$item[0]] = array($content['view']['records'][$key][$item[0]]);
						$content['view']['records'][$key][$item[0]][] = $item[1];
					} else {
						$content['view']['records'][$key][$item[0]] = $item[1];
					}
				}
				if(isset($content['view']['item_images'][$source][$record_id])) {
					foreach($content['view']['item_images'][$source][$record_id] as $item) {
						if(isset($content['view']['records'][$key]['image'])) {
							if(!is_array($content['view']['records'][$key]['image'])) {
								$content['view']['records'][$key]['image'] = array($content['view']['records'][$key]['image']);
							}
							$content['view']['records'][$key]['image'][] = array(
								'url' => $item['link'],
								'tag' => $item['tag']
							);
						} else {
							$content['view']['records'][$key]['image'][] = array(
								'url' => $item['link'],
								'tag' => $item['tag']
							);
						}
					}
				}
				$key ++;
			}
		}

		$json_array['title'] = $content['view']['title'][0];
		$json_array['description'] = $content['view']['description'][0];
	}

	if(preg_match('@(maa|fitz|whipple)/item@', $request_parameters['this_url'], $matches) == true) {
		$content['view']['record'] = array(
					'id' => array('_id', $request_parameters['app_elements']['item']),
					'type' => array('_type', $matches[1])
				) + $content['view']['record'];
		foreach($content['view']['record'] as $item) {
			$content['view']['records'][0][$item[0]] = $item[1];
		}
		if(!empty($content['view']['images'])) {
			foreach($content['view']['images'] as $item) {
				if(isset($content['view']['records'][0]['image'])) {
					if(!is_array($content['view']['records'][0]['image'])) {
						$content['view']['records'][0]['image'] = array($content['view']['records'][0]['image']);
					}
					$content['view']['records'][0]['image'][] = preg_replace('@75/@', '', $item['url']);
				} else {
					$content['view']['records'][0]['image'] = preg_replace('@75/@', '', $item['url']);
				}
			}
		}
	}

//	print_r($content['view']['records']);

	$json_array['record_count'] = 0;
	if(isset($content['view']['records'])) {


		// Convert Content to json string:
		$json_array['record_count'] = count($view[$controller]['content']);
		$json_array['records'] = $content['view']['records'];

	}
*/
	$content =  array('url' => preg_replace('@json@', '', $request_parameters['request_url']));
	$content = array_merge($content, $view[$controller]);
	$json_output = json_encode($content);
	preg_replace('@path\: "@', 'path: "'.$request_parameters['domain_url'], $json_output);

//	print_r(json_decode($json_output));

	// Send the content to the view:
	require(loadMVC('view', 'shared/json'));

?>