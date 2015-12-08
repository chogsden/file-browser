<?PHP

	$header_items = array();

	if(!isset($view[$controller]['title'])) {
		$view[$controller]['title'] = '';
	}

/*
	// Show images only toggle:
	switch($request_parameters['app_elements']['view']) {
		case 'images':
			$view_html = '
			<a href="'.$request_parameters['base_uri'].'" class="btn btn-default" role="button">all</a>
			<div class="btn btn-default active" role="button">images</div>
			<a href="'.$request_parameters['base_uri'].'view=text/" class="btn btn-default" role="button">text</a>
			';
			break;
		case 'text':
			$view_html = '
			<a href="'.$request_parameters['base_uri'].'" class="btn btn-default" role="button">all</a>
			<a href="'.$request_parameters['base_uri'].'view=images" class="btn btn-default" role="button">images</a>
			<div class="btn btn-default active" role="button">text</div>
			';
			break;
		default:
		$view_html = '
		<div class="btn btn-default active" role="button">all</div>
		<a href="'.$request_parameters['base_uri'].'view=images/" class="btn btn-default" role="button">images</a>
		<a href="'.$request_parameters['base_uri'].'view=text/" class="btn btn-default" role="button">text</a>
		';
	}

	$header_items[] = '
	<div class="btn-group">
		'.$view_html.'
	</div>
	';
*/
	$header_items[] = '
	<h3>'.$view[$controller]['title'].'</h3>
	<br />
	<h5>Using '.$controller.'</h5>
	<hr class="border-bottom"/>
	';

/*
	// Search box:
	$header_items[] = '
	<form accept-charset="UTF-8" action="/'.$request_parameters['client_request'].'" class="main_form" method="get">
		<div style="margin:0;padding:0;display:inline">
			<input name="utf8" type="hidden" value="✓">
		</div>

		<div class="autocomplete_input_wrapper">
			<input autocapitalize="off" autocomplete="off" autocorrect="off" class="main_form_input" data-autocomplete-url="/aofe_encounters/autocomplete" id="q" name="q" placeholder="Filter..." size="50" type="text">
			<ul class="autocomplete_choices_container" style="display: none;"></ul>
		</div>
		<button class="main_form_submit btn btn-success" name="button" type="submit">
		<i class="icon-search icon-white"></i></button>
	</form>
	';

*/


	$view['header'] = '
	<div class=page-header">
		'.implode('', $header_items).'
	</div>
	';

?>