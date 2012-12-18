<?php echo $this -> Html -> script('pages/page.activities.index', array('inline' => false)); ?>

<?php
// I see this getting really messy really fast
foreach ($activites as $key => $activity) {
	echo '<div class="row activity">';

	echo '<div class="span2 actor">' . $activity['Activity']['data'] -> actor -> displayName . '</div>';
	echo '<div class="span6 action">';
	// For the title, we need to construct: User verb object target
	echo '<div class="row title">';

	if ($activity['Activity']['activity_type_id'] === '6') {
		echo '<div class="span5">' . $activity['Activity']['data'] -> verb . ' ' . $activity['Activity']['data'] -> object -> objectType . '</div>';

	} else {
		echo '<div class="span5">' . $activity['Activity']['data'] -> verb . ' ' . $activity['Activity']['data'] -> object -> objectType . ' ';
		if ($activity['Activity']['data'] -> target -> objectType === 'collectible') {
			if (is_null($activity['Activity']['data'] -> target -> displayName)) {
				echo 'Collectible';
			} else {
				echo $activity['Activity']['data'] -> target -> displayName;
			}
		}
		echo '</div>';

	}
	echo '</div>';
	// end title

	echo '<div class="row object">';
	echo '<div class="span5">' . $activity['Activity']['data'] -> object -> id . '</div>';
	echo '</div>';
	// end object
	echo '</div>';
	// end action
	echo '</div>';
	// end row activity
}
?>