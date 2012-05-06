<?php
class CollectiblesTag extends AppModel {

	public $name = 'CollectiblesTag';
	public $belongsTo = array('Collectible', 'Tag' => array('counterCache' => true), 'Revision');
	public $actsAs = array('Containable', 'Editable' => array('type' => 'tag', 'model' => 'CollectiblesTagEdit'));

	function getUpdateFields($tagEditId, $notes = null) {
		//Grab out edit collectible
		$tagEditVersion = $this -> findEdit($tagEditId);
		//reformat it for us, unsetting some stuff we do not need
		debug($tagEditVersion);
		$tagFields = array();
		if ($tagEditVersion['CollectiblesTagEdit']['action'] === 'A') {
			$tagFields['CollectiblesTag']['tag_id'] = $tagEditVersion['CollectiblesTagEdit']['tag_id'];
			$tagFields['CollectiblesTag']['collectible_id'] = $tagEditVersion['CollectiblesTagEdit']['collectible_id'];
			$tagFields['Revision']['action'] = 'A';
		} else if ($tagEditVersion['CollectiblesTagEdit']['action'] === 'D') {
			//For deletes, lets set the status to 0, that means it is not active
			// $tagFields['CollectiblesTag']['active'] = 0;
			$tagFields['CollectiblesTag']['id'] = $tagEditVersion['CollectiblesTagEdit']['base_id'];
			$tagFields['Revision']['action'] = 'D';
		}
		// else if ($tagEditVersion['CollectiblesTagEdit']['action'] === 'E') {
		// //The only thing we can edit right now is the description
		// $tagFields['CollectiblesTag']['description'] = $tagEditVersion['CollectiblesTagEdit']['description'];
		// $tagFields['CollectiblesTag']['id'] = $tagEditVersion['CollectiblesTagEdit']['base_id'];
		// $tagFields['Revision']['action'] = 'E';
		// }

		if (!is_null($notes)) {
			$tagFields['Revision']['notes'] = $notes;
		}
		//Make sure I grab the user id that did this edit
		$tagFields['Revision']['user_id'] = $tagEditVersion['CollectiblesTagEdit']['edit_user_id'];
		return $tagFields;
	}

}
?>