<?PHP
	// TEMPLATE 2 ITEM View //

	// Set display html for controller:

	$html_data = array();
	$html_media = array();
	$html_sidebar = '';

	require(loadMVC('view', 'shared/item'));
	require(loadMVC('view', 'shared/page_header'));
//	require(loadMVC('view', 'shared/sidebar'));
	
	// Set display html:
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

	<section id="content3" class="section">
	<div class="container">
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-12 margin-20">
					'.implode('', $html_data).'
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3 class="black">Media</h3>
					'.implode('', $html_media).'
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			'.$html_sidebar.'
		</div>
	</section>
	';

	// Set additional javascript:
	$js_content = '';
?>