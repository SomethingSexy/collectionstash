<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
$urlparams = $this -> request -> query;
unset($urlparams['url']);
$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
if($this -> Paginator -> last()){
	$this->response->header('Link', '</collectibles_users/collectibles/'.$username.'?per_page=25>; rel="next"');
}
echo $this -> Js -> object($collectibles);
?>