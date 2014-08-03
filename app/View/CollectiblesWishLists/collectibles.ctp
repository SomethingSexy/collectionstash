<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
if($this -> Paginator -> last()){
	$this->response->header('Link', '</collectibles_wish_lists/collectibles/'.$username.'?per_page=25>; rel="next"');
}
$response = array();
array_push($response, array('page' => $paging['page'], 'per_page' => $paging['limit'], 'total_pages' => $paging['pageCount'], 'total_entries' => $paging['count']));
array_push($response, $collectibles);

echo $this -> Js -> object($response);
?>