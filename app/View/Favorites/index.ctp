<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
$urlparams = $this -> request -> query;
unset($urlparams['url']);
$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
if($this -> Paginator -> last()){
	$this->response->header('Link', '</favorites/index/'.$username.'?per_page=25>; rel="next"');
}

// $response = array();
// array_push($response, array('page' => $paging['page'], 'per_page' => $paging['limit'], 'total_pages' => $paging['pageCount'], 'total_entries' => $paging['count']));
// array_push($response, $uploads);

echo $this -> Js -> object($favorites);
?>