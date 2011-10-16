<?php
class AttributesCollectiblesController extends AppController {

	var $name = 'AttributesCollectibles';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler');

	/**
	 * This method will return the history for a given attributes collectible
	 */
	function history($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			//Date and timestamp of update and user who did the update
			$this -> AttributesCollectible -> id = $id;
			$history = $this -> AttributesCollectible -> revisions(null, true);
			//As of 9/7/11, because of the way we have to add an attributes collectible, the first revision is going to be bogus.
			//Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
			$lastHistory= end($history);
			if ($lastHistory['AttributesCollectible']['revision_id'] === '0') {
				array_pop($history);
			}
			reset($history);
			debug($history);
			$this -> set(compact('history'));

		} else {
			$this -> redirect($this -> referer());
		}
	}

}
?>