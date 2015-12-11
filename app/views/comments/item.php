<?PHP
	// MAA ITEM View //

	// Set display html for controller:

	$form_wrapper_open = '';
	$form_wrapper_close = '';
	$html_data = array();
	$html_media = array();
	$html_description = '';
	$html_collections = '';
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

	'.$form_wrapper_open.'
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
	'.$form_wrapper_close.'

	'.$html_collections.'
	';

	// Set additional javascript:
	$js_content = '
	<script>
		$(document).ready(function () {
		    $("input#submit-text").click(function(){		    
		        $.ajax({
		            type: "POST",
		            url: "'.$config['domain'].$config['root_dir'].'text_upload/",
		            data: $(\'form.upload-content\').serialize(),
		            success: function(data) {
					    if(data.status == "success") {
					        alert("Related text creted successfully!");
					        location.reload();
					    } else if(data.status == "failed"){
					        alert("Error! Related text not created");
					    }
					}
		        });
		    });

		    $("input#submit-items").click(function(){	   
		        $.ajax({
		            type: "POST",
		            url: "'.$config['domain'].$config['root_dir'].'collection_items/view=collect",
		            data: $(\'form.add-to-collection\').serialize(),
		            success: function(data) {
					    if(data.status == "success") {
					    	alert("Collected items successfully!");
					    } else if(data.status == "failed"){
					        alert("Error! Failed to collect all items");
					    }
					    document.location = \''.$config['domain'].$config['root_dir'].'maa/item='.$request_parameters['app_elements']['item'].'/\';
					}
		        });
		    });
		});
	</script>
	';
?>