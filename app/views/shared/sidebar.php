<?PHP

	// Sidebar:

	$html_relations = array();
	$html_comments = array();
	$relations_count = 0;

	$count = 0;
	if(!empty($content['view']['sidebar']['relations'])) {
		foreach($content['view']['sidebar']['relations'] as $related_type => $related_items) {
			$html_relations[] = '
			<div class="panel-heading">
				<h4 class="panel-title bold">'.ucfirst(preg_replace('@_@', ' ', $related_type)).':</h4>
			</div>';
			foreach($related_items as $field_id => $item) {
				$checkbox = '';
				if($request_parameters['app_elements']['view'] == 'collect') {
					$checkbox = '&nbsp;<input name="relation/'.$item['id'].'" type="checkbox" />';
				}
				switch($related_type) {
					case 'media':
						if($item['field_id'] == 38) {
							$html_relations[] = '
							<div class="border-bottom panel-body">
								<a href="'.$item['path'].'" class="disabled">
									<div>
										<p><img src="'.$item['content']['media_path'].'75/" alt="'.$item['content']['caption'].'" /></p>
										<!-- <p>'.$item['content']['caption'].'</p> -->
									</div>
								</a>
								'.$checkbox.'
							</div>
							';
						}
						break;

					case 'text':
						$html_relations[] = '
						<div class="border-bottom panel-body">
							<dt>'.$item['field'].':</dt>
							<dd>'.$item['content']['caption'].'</dd>
							'.$checkbox.'
						</div>
						';
						break;

				}
				
			}
			$html_relations[] = '';
			$count = $count + count($related_items);
		}
		$relations_count = $count;
	}

	if(!empty($content['sidebar']['comments'])) {
		foreach($content['sidebar']['comments'] as $comment_id => $comment) {
			$html_comments[] = '
			<dt>'.$comment['name'].'</dt>
			<dd>'.$comment['body'].'</dd>
			';
		}
	}

	// Build field menu for text upload:
	$menu = array();
	foreach($application_fields as $field_id => $field) {
		$menu[] = '<option value="'.$field_id.'">'.$field.'</option>';
	}
	$text_type_menu = '
	<select name="field-id" class="form-control source-field">
		<option selected></option>
		'.implode('', $menu).'
	</select>
	';

	$html_sidebar = '
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="padding-t-0">Related Items <span class="superscript badge">'.$relations_count.'</span></h3>
			<a data-toggle="modal" href="#form-content" class="btn btn-primary btn-sm">Add</a>
		</div>
		'.implode('', $html_relations).'
	</div>
	<div class="panel panel-default">
		<h3>Discussion <span class="superscript badge">'.count($html_comments).'</span></h3>
		'.implode('', $html_comments).'
	<div>

	<div id="form-content" class="modal fade" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="bold">Add a related item...</h4>
					<a class="close" data-dismiss="modal">×</a>

					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="text">
							<a href="#text" aria-controls="profile" role="tab" data-toggle="tab">Text</a>
						</li>
						<!--
						<li role="object">
							<a href="#object" aria-controls="messages" role="tab" data-toggle="tab">Object</a>
						</li>
						-->
					</ul>
				</div>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane" id="text">
							<div class="modal-body">
								<form class="upload-content" name="upload-content" method="post">
									<input type="hidden" name="relator-id" value="'.$request_parameters['app_elements']['item'].'" />
									<input type="hidden" name="relator-type" value="'.$request_parameters['app_elements']['section'].'" />
									<br />
									<label for="field">Chose a field:</label>
									<br />
									'.$text_type_menu.'
									<br />
									<label for="text">Enter text:</label><br>
									<textarea name="text" class="form-control" rows="5"></textarea>
									<br />
									<label for="reason">Enter reason:</label><br>
									<textarea name="notes" class="form-control" rows="5"></textarea>
								</form>
							</div>
							<div class="modal-footer">
								<input class="btn btn-success" type="submit" value="create" id="submit-text" />
								<a href="#" class="btn" data-dismiss="modal">cancel</a>
							</div>
						</div>
						<!--
						<div role="tabpanel" class="tab-pane" id="object">
							<div class="modal-body">
							</div>
							<div class="modal-footer">
								<input class="btn btn-success" type="submit" value="create" id="submit-object" />
								<a href="#" class="btn" data-dismiss="modal">cancel</a>
							</div>
						</div>
						-->
					</div>
					<!-- End panes -->
				</div>
			</div>
		</div>
	</div>
	';

?>