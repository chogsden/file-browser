<?PHP

	// Thumbnail Image Grid:

//	print_r($grid['items']);
	$grid_items = array('no records');
	if(!empty($grid['items'])) {
		$grid_items = array();
		foreach($grid['items'] as $record) {
			$id = $record['id'];
			$image = '
			<div class="image">
				<img src="'.$record['path'].'" width="150" title="'.$record['name'].'" alt="image of '.$record['name']	.'" />
			</div>
			';
			$grid_items[] = '
			<div class="grid-item">
				<a href="'.$request_parameters['request_url'].'/item='.$record['parent'].'/">
					'.$image.'
				</a>
			</div>
			';
		}
	}
	$grid['view'] = '
	<div class="image-grid">
		'.implode('', $grid_items).'
	</div>
	';

?>