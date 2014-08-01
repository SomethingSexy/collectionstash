<?php
if($this -> Paginator -> last()){
	$this->response->header('Link', '</activites?per_page=10>; rel="next"');
}
$response = array();
array_push($response, array('page' => $paging['page'], 'per_page' => $paging['limit'], 'total_pages' => $paging['pageCount'], 'total_entries' => $paging['count']));
array_push($response, $collectibles);
echo $this -> Js -> object($response);
?>
