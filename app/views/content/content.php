
<?PHP
	// MAA View //

	// Set display html for controller:

	if(isset($view['settings']['style']['template'])) {
		require(loadMVC('view', 'shared/template'.$view['settings']['style']['template']));
	}

	require(loadMVC('view', 'shared/pages'));
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
					<div class="text-left">'.$display.'</div>
				</div>
			</div>
		</div>
	</section>

	<section id="pages">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					'.$pagination.'
				</div>
			</div>
		</div>
	</section>
	';

	// Set additional javascript:
	$$view['js'] = '';
?>