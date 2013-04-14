<?php
// Build out a similar structure for this search result
$paging = $this -> Paginator -> params();
$urlparams = $this -> request -> query;
unset($urlparams['url']);
$returnData = array();
$returnData['metadata'] = array();
$returnData['metadata']['paging'] = $paging;
$returnData['metadata']['url'] = $this -> Paginator -> url();
$returnData['metadata']['params'] = $urlparams;
$returnData['results'] = $attributes;

// Might be easier for me to build out all of the paging HTMl here
$pagingHtml = '';
$pagingHtml .= '<p>';
$pagingHtml .= $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
$pagingHtml .= '</p>';
$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
$pagingHtml .= $this -> Paginator -> numbers(array('separator' => false));

$pagingHtml .= $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));

$returnData['metadata']['pagingHtml'] = $pagingHtml;

echo $this -> Js -> object($returnData);
?>