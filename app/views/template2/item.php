<?PHP
	// TEMPLATE 2 ITEM View //

	// Set display html for controller:

	$html_data = array();
	$html_media = array();
	$html_sidebar = '';

	require(loadMVC('view', 'shared/item'));
	require(loadMVC('view', 'shared/page_header'));
	require(loadMVC('view', 'shared/sidebar'));
	
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

	<!-- Begin content for Comments drop-down window -->
	<div id="form-comment" class="modal fade" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bold">
					Submit a Comment:
					<a class="close" data-dismiss="modal">×</a>
				</div>
				<div class="modal-body">
					<form class="submit-comment" name="submit-comment" method="post">
						<input type="hidden" name="accept" value="1" />
						<label for="comment">Comment:</label><br>
						<textarea name="comment" class="form-control" rows="5"></textarea>
					</form>
				</div>
				<div class="modal-footer">
					<input class="btn btn-success" type="submit" value="Create" id="submit-comment">
					<a href="#" class="btn" data-dismiss="modal">cancel</a>
				</div>
			</div>
		</div>
	</div>
	<!-- End -->
	';

	// Set additional javascript:
	$view['js'] = '
	<script>
		$(document).ready(function () {
		    $("input#submit-comment").click(function(){		    
		        $.ajax({
		            type: "POST",
		            url: "'.$request_parameters['base_url'].'comments/",
		            data: $(\'form.comment\').serialize(),
		            success: function(data) {
					    if(data.status == "success") {
					        alert("Your comment has been submitted");
					        location.reload();
					    } else if(data.status == "failed"){
					        alert("Error! Submission failed");
					    }
					}
		        });
		    });
		});
	</script>';