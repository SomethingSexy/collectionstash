<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
$urlparams = $this -> request -> query;
unset($urlparams['url']);

$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));


$pagingHtml .= $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));

$this->response->header('Link', '</collectibles_users/collectibles/ComingOfShadows?page=2&per_page=25>; rel="next"');
echo $this -> Js -> object($collectibles);
?>