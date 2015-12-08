<?PHP
	// TEMPLATE 4 View //

	// Set display html for controller:

	$display = array('
		<h3>'.$view[$controller]['content']['intro'].'</h3>
		<hr class="border-bottom"/>
		'
	);

	foreach($view[$controller]['content']['items'] as $id => $record) {
		$display_items = array();
		foreach($record as $field) {
			if($field['type'] == 'image') {
				$display_items[] = '<img src="'.$field['path'].$field['parent'].'/'.$field['name'].'.jpg" width="300" />';
			} else {
				$display_items[] = '
				<dt>'.ucfirst(preg_replace('@_@', ' ', $field['name'])).':</dt>
				<dd>'.$field['content'].'</dd>
				';
			}
		}
		$display[] = '<div>'.implode($display_items).'</div>';
		$display[] = '<hr class="border-bottom" />';
	}


	require(loadMVC('view', 'shared/page_header'));

	// Set display html for view:
	$view['body'] = '
	<!--Main Content Section-->

	<section id="page-header" class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					'.$view['header'].'
				</div>
			</div>
		</div>
	</section>

	<section id="content1" class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="text-left">'.implode($display).'</div>
				</div>
			</div>
		</div>
	</section>
	';

	// Set additional javascript:
	$js_content = '';
?>