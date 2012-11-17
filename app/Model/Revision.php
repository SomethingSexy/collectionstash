<?php
/**
 * This should eventually get the action model
 */
class Revision extends AppModel {
	public $name = 'Revision';
	public $actsAs = array('Containable');
	public $belongsTo = array('User' => array('fields' => array('id', 'username')));

	public $ADD = 'A';
	public $EDIT = 'E';
	public $APPROVED = 'P';

	public function buildRevision($userId, $action, $notes = null) {
		$revision = array();
		$revision['Revision']['user_id'] = $userId;
		if (!is_null($notes)) {
			$revision['Revision']['notes'] = $notes;
		}
		$revision['Revision']['action'] = $action;
		return $revision;
	}

}
?>
