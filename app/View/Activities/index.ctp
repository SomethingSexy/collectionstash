
<?php
// I see this getting really messy really fast
// foreach ($activites as $key => $activity) {
	// echo '<div class="row activity">';
// 
	// echo '<div class="span2 actor">' . $activity['Activity']['data'] -> actor -> displayName . '</div>';
	// echo '<div class="span6 action">';
	// // For the title, we need to construct: User verb object target
	// echo '<div class="row title">';
// 
	// if ($activity['Activity']['activity_type_id'] === '12') {
		// echo '<div class="span5">' . $activity['Activity']['data'] -> verb . ' ';
		// if (is_null($activity['Activity']['data'] -> target -> displayName)) {
			// echo 'Collectible';
		// } else {
			// echo '<a href="' . $activity['Activity']['data'] -> target -> url . '">' . $activity['Activity']['data'] -> target -> displayName . '</a>';
		// }
		// echo '</div>';
	// } else {
		// echo '<div class="span5">' . $activity['Activity']['data'] -> verb . ' ';
// 
		// // TODO: We need to find type collectible and type id === 6 and handle both new and old format
// 
		// if ($activity['Activity']['data'] -> object -> objectType === 'photo' && $activity['Activity']['activity_type_id'] === '5') {
			// // need to handle old format, that does not contain the name
			// if (strstr($activity['Activity']['data'] -> object -> url, '.')) {
				// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '">' . $activity['Activity']['data'] -> object -> objectType . '</a> ';
			// } else {
				// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '/' . $activity['Activity']['data'] -> object -> data -> name . '">' . $activity['Activity']['data'] -> object -> objectType . '</a> ';
			// }
		// } else if ($activity['Activity']['data'] -> object -> objectType === 'collectible') {
			// if ($activity['Activity']['activity_type_id'] === '6' || $activity['Activity']['activity_type_id'] === '8') {
				// if (isset($activity['Activity']['data'] -> object -> data -> type)) {// old api
					// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '">' . $activity['Activity']['data'] -> object -> data -> displayName . '</a> ';
				// } else {// current
					// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '">' . $activity['Activity']['data'] -> object -> data -> Collectible -> displayTitle . '</a> ';
				// }
			// } else {
				// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '">' . $activity['Activity']['data'] -> object -> data -> Collectible -> displayTitle . '</a> ';
			// }
		// } else if ($activity['Activity']['data'] -> object -> objectType === 'attribute') {
			// echo '<a href="' . $activity['Activity']['data'] -> object -> url . '"> part ' . $activity['Activity']['data'] -> object -> data -> Attribute  -> name  . '</a> ';
		// } else {
			// echo $activity['Activity']['data'] -> object -> objectType . ' ';
		// }
// 
		// if (isset($activity['Activity']['data'] -> target)) {
// 
			// if ($activity['Activity']['data'] -> verb === 'approve') {
				// echo 'submitted by ';
			// } else {
				// echo 'to ';
			// }
			// echo '<a href="' . $activity['Activity']['data'] -> target -> url . '">' . $activity['Activity']['data'] -> target -> displayName . '</a>';
		// }
		// echo '</div>';
// 
	// }
	// echo '</div>';
	// // end title
// 
	// //echo '<div class="row object">';
	// //echo '<div class="span5">' . $activity['Activity']['data'] -> object -> id . '</div>';
	// //echo '</div>';
	// // end object
	// echo '</div>';
	// // end action
	// echo '</div>';
	// // end row activity
// }
?>

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
$returnData['results'] = $activities;

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
