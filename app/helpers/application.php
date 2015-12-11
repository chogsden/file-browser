<?PHP

	// Google Analytics API setup:
	if($config['google_analytics'][0] == true AND isset($config['google_analytics'][1])) {
		$view['analytics'] = '
		<!--Google Analytics-->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push([\'_setAccount\', \''.$config['google_analytics'][1].'\']);
			_gaq.push([\'_trackPageview\']);

			(function() {
				var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
				ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
				var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		';
	}

/*	
	// Client browser caching to do:
	$mod_time = filemtime($image_path.'tiny/'.$filename);
	$expires = 604800;

	header("Content-type: image/jpeg");
	header("Cache-Control: private, max-age=".$expires.", pre-check=".$expires."");
	header("Expires: " . gmdate('D, d M Y H:i:s', strtotime( '+'.$expires.' seconds')) . " GMT");

	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		// if the browser has a cached version of this image, send 304
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', $mod_time).' GMT');
		header("HTTP/1.1 304 Not Modified");
		die;
	} elseif(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $mod_time)) {
		// option 2, if you have a file to base your mod date off:
		// send the last mod time of the file back
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mod_time).' GMT',true, 304);
		header("HTTP/1.1 304 Not Modified");
		die;
	} else {
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', $mod_time).' GMT');
	}
*/

	// Clear development log:
	deleteLog($config);

?>