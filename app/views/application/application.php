<!DOCTYPE html>
<html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>File-Browser</title>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="viewport" content="width=device-width">

		<!-- Styles -->
		<?PHP echo('
		<link rel="stylesheet" href="'.$request_parameters['base_url'].'css/font-awesome.css">
		<link rel="stylesheet" href="'.$request_parameters['base_url'].'css/animate.css">
		<link rel="stylesheet" href="'.$request_parameters['base_url'].'css/bootstrap.css">
		<link rel="stylesheet" href="'.$request_parameters['base_url'].'css/custom-styles.css">
		'); ?>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic" type="text/css">
	</head>
	<body>

		<?PHP 
		// <!--Google API-->
		echo($view['analytics']);

		// <!--Header Content-->
		echo($view['navbar']);

		// <!--Main Content-->
		echo($view['body']);

		// <!--Footer Content-->
		echo($view['footer']);
		?>
    
		<!-- Javascript -->
		<?PHP echo('
		<!-- jQuery ====================================================================== -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>

		<!-- Bootstrap =================================================================== -->
		<script src="'.$request_parameters['base_url'].'jscript/bootstrap.min.js"></script>
		
		<!-- Load content specific js -->

		'.$view['js'].'

		'); ?>

	</body>
</html>

