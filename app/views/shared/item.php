<?PHP

	// Single item:
	
	$html_data = array('no data available');
	$html_media = array();

	if(!empty($view[$controller]['content']['items'])) {

		$html_data = array();

		foreach ($view[$controller]['content']['items'] as $id => $record) {
			if($record['type'] == 'image') {
				$html_media[] = '
				<a href="" class="media_thumbnail" data-preview="" target="_blank">
					<img src="'.$record['path'].$record['parent'].'/'.$record['name'].'.jpg" alt="image" width="500" title="" />
				</a>';
			} else {
				$html_data[] = '
				<dt>'.ucfirst(preg_replace('@_@', ' ', $record['name'])).':</dt>
				<dd>'.preg_replace('@'.chr(10).'@', '<br />', $record['content']).'</dd>
				';
			}
		}

	}
/*
		if(isset($content['view']['collection'])) {
			foreach($html_data as $field_id => $html) {
				$html_data[$field_id] = preg_replace('@</dd>@', '&nbsp;<input name="record/'.$field_id.'" type="checkbox" /></dd>', $html_data[$field_id]);
			}
			$collect_btn = '
			<div class="btn-collect"> 
				<h5 id="collection-name">add items to '.$content['view']['collection']['title'].'</h5>
				<input class="btn btn-success" type="submit" value="Collect" id="submit-items" />
				<a href="'.$config['domain'].$config['root_dir'].$request_parameters['app_elements']['section'].'/item='.$request_parameters['app_elements']['item'].'/" class="btn btn-danger">cancel</a>
			</div>
			';
			$form_wrapper_open = '
			<form method="post" class="add-to-collection" name="add-to-collection">
				<input type="hidden" name="collection-id" value="'.$content['view']['collection']['id'].'" />
			';
			$form_wrapper_close = '</form>';
		}

		array_unshift($html_data, '
		<dl class="dl-horizontal">
		');
		$html_data[] = '
		</dl>
		';

//		print_r($html_data);
	}

	if(!empty($content['view']['collections'])) {
		$menu = array();
		foreach($content['view']['collections'] as $collection_id => $collection) {
			$menu[] = '<option value="'.$collection['id'].'-'.$collection['title'].'">'.$collection['title'].'</option>';
		}
		$collections_menu = '
		<select name="collection" class="form-control source-field">
			<option selected></option>
			'.implode('', $menu).'
		</select>
		';

		$html_collections = '
		<!-- Begin content for Collections drop-down window -->
		<div id="form-collection" class="modal fade" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bold">
						Choose a Collection:
						<a class="close" data-dismiss="modal">×</a>
					</div>
					<div class="modal-body">
						<form method="post" action="'.$config['domain'].$config['root_dir'].$request_parameters['app_elements']['section'].'/item='.$request_parameters['app_elements']['item'].'/view=collect">
							'.$collections_menu.'
							<div class="modal-footer">
								<input class="btn btn-success" type="submit" value="Collect">
								<a href="#" class="btn" data-dismiss="modal">cancel</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- End -->
		';
	}	

	if(isset($content['view']['images'])) {
		$html_media = array();
		foreach($content['view']['images'] as $field_id => $image_data) {
			$checkbox = '';
			if($request_parameters['app_elements']['view'] == 'collect') {
				$checkbox = '&nbsp;<input name="record/'.$field_id.'" type="checkbox" />';
			}
			$html_media[] = '
			<a href="'.$image_data['link'].'" class="media_thumbnail" data-preview="'.$image_data['link'].'" target="_blank">
				<img src="'.$image_data['url'].'" alt="image of '.$image_data['tag'].'" title="'.$image_data['tag'].'" width="75" />
			</a>
			'.$checkbox.'
			';
		}
	}



	if(isset($content['view']['title'])) {
		$html_title = $content['view']['title'];
	}
*/

?>