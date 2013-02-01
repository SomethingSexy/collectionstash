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
$returnData['results'] = $collectibles;

// Might be easier for me to build out all of the paging HTMl here
$pagingHtml = '';
$pagingHtml .= '<p>';
$pagingHtml .= $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
$pagingHtml .= '</p>';
$pagingHtml .= $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
$pagingHtml .= $this -> Paginator -> numbers(array('separator' => false));

$pagingHtml .= $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));

$returnData['metadata']['pagingHtml'] = $pagingHtml;

//
// {
// "metadata": {
// "host": "api.examplestore.com",
// "path": "\/catalog\/search",
// "query": "query=manafucturer+name&facets%5B0%5D=fieldType%3Arelease",
// "page": 1,
// "perPage": 10,
// "count": 209,
// "totalPages": 21,
// "nextQuery": "query=manafucturer+name&facets%5B0%5D=fieldType%3Arelease&page=2",
// "perPageOptions": [
// {
// "value": 50,
// "applyQuery": "query=manafucturer+name&facets%5B0%5D=fieldType%3Arelease&perPage=50"
// },
// {
// "value": 100,
// "applyQuery": "query=manafucturer+name&facets%5B0%5D=fieldType%3Arelease&perPage=100"
// },
// {
// "value": 150,
// "applyQuery": "query=manafucturer+name&facets%5B0%5D=fieldType%3Arelease&perPage=150"
// }
// ],
// "facets": {
// "fields": {
// "": [
//
// ]
// }
// },
// "appliedFacets": [
//
// ],
// "spellcheck": {
// "suggestions": [
//
// ]
// }
// },
// "results": [
// {
// "id": 188183,
// "type": "product",
// "name": "Product Name",
// "slug": "product-slug",
// "release": "2012-12-11",
// ...
// },
// ... //next 9 results
// }

echo $this -> Js -> object($returnData);
?>
