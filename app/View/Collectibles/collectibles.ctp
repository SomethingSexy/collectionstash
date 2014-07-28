<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
$urlparams = $this -> request -> query;
unset($urlparams['url']);
$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));

$response = array();
array_push($response, array('page' => $paging['page'], 'per_page' => $paging['limit'], 'total_pages' => $paging['pageCount'], 'total_entries' => $paging['count']));
array_push($response, $collectibles);

echo $this -> Js -> object($response);
?>