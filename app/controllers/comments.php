<?PHP

	// CONTENT Controller //

	if(!empty($_POST)) {

		echoContent($_POST);


		if($_POST['accept'] == true){

			print_r($_POST);

			// Set model parameters:
			$model[$controller]['selector'] = 'CREATE';
			$model[$controller]['criteria'] = array(
				'items' => array($_POST),
				'report' => true
			);

			// Send POST to the COMMENTS model:
			$model[$controller]['query'] = setModelParameters($model[$controller]['criteria']);
//			require(loadMVC('model', 'comments'));

		}

		// Unset Application view:
		$request_parameters['output_format'] = '_null';

	} else {


	}

	echoContent($view);

?>