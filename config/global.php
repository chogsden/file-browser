<?PHP

	// Set server domain name:
	if(!isset($_SERVER['SERVER_NAME'])) {
		$_SERVER['SERVER_NAME'] = '';
	}
	
	// Application configuration:
	$config = array(

		// Server domain name:
		'domain_url'		=>	'http://'.$_SERVER['SERVER_NAME'].'/',
		
		// Application root directory:
		'root_dir'			=>	'file-browser/',

		// Path to root directory:
		'root_path'			=>	'',

		// Source for importing additional routes:
		'routes'			=>	true,

		// Paths to image directories:
		'media_path'		=>	'media/',

		// Enable / diasable site authentication:
		'authentication'	=>	array(
			'enable'		=>	false,
			'cookie_name'	=>	'UCM-publsher',
		),

		// Enable / disable development log
		//	- only enable for debugging purposes, log file will build up rapidly!:
		'development_log'	=> false,

		// Path to development log file:
		'dev_log_path' 		=> 'app/log/development.log',

		// Client mobile devices to identify:
		'mobile_agents' 	=> array(
			'iPad', 'iPhone', 'Android', 'webOS', 'BlackBerry', 'Windows Phone', 'Nokia'
		),

		// Media upload settings:

			// accepted media types:
			'media_types' => array('jpg'),

			// Path to Media Upload directory: 
			'media_upload_path'	=>	'media_upload/',

			//Path to Image Magick convert for image maninpualtion:
			'convert_path'		=>	'',

		// Google analytics settings:
		'google_analytics'	=>	array(
		
			// Include Google Analytics:
			false, 
		
			//Google Analytics account:
			''
		
		),

		// CSS class names according to text markup type for customised text display:
		'text_markup'		=>	array(

			// Type:		// Class name:
			''			=>	''

		),

		// Enter additional lookup rules to validate against URL request.
		'url_validation_rules' => array(

			'person' => '[a-z]+',
			'type' => 'relations|text|media',
			'view' => 'collect',

			// This is additional to http://host/root_dir/route_request/item=*/page=*/view=*/type=*
			// Where: 
			//	page=n, 
			//	view=images,text,uploader,importer,collect	
			//	type=		// e.g. to only parse numbers in addition to the default allowance for anything appearing after http://host/root_dir/route_request/:
			// (^[1-9][0-9]*$)

			
		),

		// List of accepted output formats for applcation content:
		'allowed_output_formats' => array(

			// view path:		// format:
			'application'	=>	'html',		// Default display format
											// Other display/output formats for content:
			'shared/json'	=>	'json',		//		JSON
			'shared/xml'	=>	'xml',		//		XML

		)

	);

	// Set default timezone:
	date_default_timezone_set('Europe/London');

//	error_reporting(false);		// use in production mode to turn off PHP error reporting


?>