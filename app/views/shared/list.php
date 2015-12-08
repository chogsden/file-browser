<?PHP

	// Thumbnail Image Grid:

	$grid_items = array();
	if(!empty($content['view']['title'])) {
		foreach($content['view']['title'] as $id => $item) {
			$description = '';
			if(!empty($content['view']['description'][$id][0])) {
				$description = $content['view']['description'][$id][0];
			}
			$list_items[] = '
			<div class="col-md-12 list-row">
				<div class="list-item">
					<a href="'.$config['domain'].$config['root_dir'].$request_parameters['client_request'].'/item='.$id.'/">
						<h3 class="bold black">'.implode(': ', $item).'</h3><h5>'.$description.'</h5>
					</a>
				</div>
			</div>
			';
		}
	} else {
		$list_items[] = 'No records found';
	}
	$record_list = '
	<div class="list-grid">
		'.implode('', $list_items).'
	</div>
	';
?>