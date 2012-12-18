<?php
App::uses('ActivityTypable', 'Lib/Activity');
// Use this to add helpers to build out the JSON object
abstract class BaseActivity extends Object implements ActivityTypable {

	public function __construct() {

		parent::__construct();
	}

	/**
	 * Default will build the user object
	 */
	protected function buildActor($type = 'user', $data) {
		$retVal = array();
		$retVal['actor'] = array();
		// if it is user then the data should be the user object
		if ($type === 'user') {
			$retVal['actor']['objectType'] = 'user';
			$retVal['actor']['id'] = $data['id'];
			$retVal['actor']['displayName'] = $data['username'];
		}

		return $retVal;
	}

	protected function buildVerb($verb) {
		return array('verb' => $verb);
	}

	/**
	 * This is the object of the activity
	 */
	protected function buildObject($id, $url, $type, $data) {
		$retVal = array();
		$retVal['object'] = array();
		$retVal['object']['id'] = $id;
		$retVal['object']['url'] = $url;
		$retVal['object']['objectType'] = $type;
		$retVal['object']['data'] = $data;
		return $retVal;
	}

	/**
	 * This is the target of the activity, like a stash
	 */
	protected function buildTarget($id, $url, $type, $displayName) {
		$retVal = array();
		$retVal['target'] = array();
		$retVal['target']['id'] = $id;
		$retVal['target']['url'] = $url;
		$retVal['target']['objectType'] = $type;

		$retVal['target']['displayName'] = $displayName;
		return $retVal;
	}

}
?>