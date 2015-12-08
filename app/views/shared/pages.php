<?PHP

    // Page Navigation:
    $page_bar = array();
    if(!empty($view['pages'])) {
        foreach($view['pages'] as $page_display => $page_link) {
            if($page_link != 'now') {
                $request_parameters['uri_elements']['page'] = $page_link;
                $page_bar[] = '
                <li>
                     <a href="'.$request_parameters['base_url'].$request_parameters['client_request'].'/'.http_build_query($request_parameters['uri_elements'], '', '/').'/">
                        '.$page_display.'
                    </a>
                </li>
                ';
            } else {
                $page_bar[] = '
                <li class="disabled">
                    <a href="#">
                        '.$page_display.' <span class="sr-only">(current)</span>
                    </a>
                </li>
                ';
            }
        }
    }

    $pagination = '
    <nav>
        <ul class="pagination">
            '.implode('', $page_bar).'
        </ul>
    </nav>
    ';
	
?>