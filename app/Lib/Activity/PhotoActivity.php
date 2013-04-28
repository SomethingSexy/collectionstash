<?php
App::uses('BaseActivity', 'Lib/Activity');
class PhotoActivity extends BaseActivity {

	private $user;

	private $photo;

	private $action;

	/**
	 * Type will be the action, add or remove
	 *
	 * For this activity we will only have a actor and an object that
	 * comes in.  The target will just be the user's gallery which we
	 * can pull from the actor for now
	 */
	public function __construct($action, $data) {
		debug($data);
		$this -> action = $action;
		$this -> user = $data['user']['User'];
		$this -> photo = $data['photo']['UserUpload'];
		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);

		// Passing in the path where the photo can be found.. passing in the upload object in as teh data
		$objectJSON = $this -> buildObject($this -> photo['id'], Configure::read('Settings.User.uploads.root-folder') . '/' . $this -> photo['user_id'] . '/' . $this -> photo['name'], 'photo', $this -> photo);
		$retVal = array_merge($retVal, $objectJSON);

		// Now add the target
		$targetJSON = $this -> buildTarget($this -> user['username'], '/user_uploads/view/' . $this -> user['username'], 'gallery', 'gallery');
		$retVal = array_merge($retVal, $targetJSON);

		$verbJSON = $this -> buildVerb($this -> action);
		$retVal = array_merge($retVal, $verbJSON);

		return $retVal;
	}

}
?>