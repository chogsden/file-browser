<?PHP
	// TEMPLATE 3 View //

	// Set display html for controller:

	$display = array();
	if(isset($view[$controller]['content']['content'])) {
		$display[] = $view[$controller]['content']['content'];
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