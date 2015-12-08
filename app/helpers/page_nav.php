<?PHP

	$pages['total'] = floor($model[$controller]['result']['record_count'] / $pages['items_per_page']);

	$page_array = array();

	$page_back = array(
		'first'	=> 1,
		'prev' 	=> $pages['request'],
	);
	$page_forward = array(
		'next' 	=> $pages['request'] + 2,
		'last' 	=> $pages['total'],

	);

	$pages_forward = 4;
	$pages_back = 4;
	$pages_total = $pages_back + $pages_forward + 1;
	$page_current = $pages['request'] + 1;

	if($model[$controller]['result']['record_count'] > $pages['items_per_page']) {

		function insertPages($page_array, $start, $count) {
//			echo(chr(10).$start.chr(10).$count.chr(10));
			for($i = $start; $i < $count; $i++) {
//				echo($i);
				$page_array[$i] = $i;
			}
			return $page_array;
		}
/*

		---- OLD CODE ---

		// If page num within first 5 pages:
		if($page_current > 1 && $page_current < 6) {
			$page_array = insertPages($page_array, 0, $page_current);
//			$page_array = insertPages($page_array, $page_current + 1, $pages_forward);
			unset($page_back['first']);
		}
		print_r($page_array);

		// If page num more than 5 and under 4 pages from last last page num:
		if($page_current > 5 && $page_current < ($pages['total'] - 4)) {
			$page_array = insertPages($page_array, $page_current - $pages_back, $pages_back);
	//		print_r($page_array);
			$page_array = insertPages($page_array, $page_current + 1, $pages_forward);
		}

		// If page num is within last 5 pages of range:	
		if($page_current < $pages['total'] - 4 && $pages['total'] > 10) {
			$page_array = insertPages($page_array, $page_current - $pages_back, $pages_back);
	//		print_r($page_array);
			$page_array = insertPages($page_array, $page_current + 1, $pages['total'] - $page_current);
			unset($page_forward['last']);
		}

		// If page num is the last page:
		if($page_current == $pages['total']) {
			unset($page_forward['next']);
			unset($page_forward['last']);
		}
*/

		if($pages['total'] < 10) {
			$page_array = insertPages($page_array, 0, $pages['total']);
			unset($page_back['first']);
			unset($page_forward['last']);
		} else {
			if($page_current <= ($pages_back + 1)) {
				$page_array = insertPages($page_array, 1, $page_current + 1);
				$page_array = insertPages($page_array, $page_current + 1, $pages_total + 1);
				unset($page_back['first']);
			}
			if($page_current > ($pages_back + 1)) {
				$page_array = insertPages($page_array, $page_current - 4, $page_current);
			}
			if($page_current < ($pages['total'] - $pages_forward)) {
				echo'here';
				$page_array = insertPages($page_array, $page_current + 1, $page_current + ($pages_forward + 1));
		//		$page_array = insertPages($page_array, floor(($pages['total']-$page_current)/2), $page_current);
			}
			if($page_current >= ($pages['total'] - $pages_forward)) {
				$page_array = insertPages($page_array, $page_current + 1, $pages['total'] + 1);
				$page_array = insertPages($page_array, $page_current - ($pages_total - ($pages['total'] - $page_current + 1)), $page_current);
				unset($page_forward['last']);
			}
		}
		// If page num is the first page:
		if($page_current == 1) {
			unset($page_back['prev']);
		}
		if($page_current == $pages['total']) {
			unset($page_forward['next']);
		}

		$page_array[$page_current] = 'now';	
		ksort($page_array);
		$page_array = $page_back + $page_array + $page_forward;
//		print_r($page_array);

	}

	$view['pages'] = $page_array;

?>