<?PHP
	// TEMPLATE 1 ITEM View //

	// Set display html for controller:

	$html_data = array();
	$html_media = array();
	$html_sidebar = '';
	$html_title = '';

	require(loadMVC('view', 'shared/item'));
//	require(loadMVC('view', 'shared/sidebar'));
	
	// Set display html:
	$view['body'] = '
	<!--Main Content Section-->
	<section id="page-title" class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1 class="black">'.implode(' - ', $html_title).'</h1>
					<hr></hr>
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