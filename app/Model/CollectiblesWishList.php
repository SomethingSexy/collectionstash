<?php
App::import('Core', 'Validation');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesWishList extends AppModel {
	public $name = 'CollectiblesWishList';
	public $belongsTo = array('WishList' => array('counterCache' => true), 'Collectible' => array('counterCache' => true), 'User' => array('counterCache' => true));
	public $actsAs = array('Containable');

	public function getCollectibleWishListCount($collectibleId, $user) {
		$retVal = $this -> find('count', array('conditions' => array('CollectiblesWishList.collectible_id' => $collectibleId, 'CollectiblesWishList.user_id' => $user['User']['id']), 'contain' => false));
		return $retVal;
	}

	public function add($data, $user) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		$retVal['response']['errors'] = array();

		if (empty($data) || empty($user)) {
			array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));
			return $retVal;
		}

		$wishList = $this -> WishList -> getWishList($user['User']['id']);
		if (!empty($wishList)) {
			$data['CollectiblesWishList']['wish_list_id'] = $wishList['WishList']['id'];
			$data['CollectiblesWishList']['user_id'] = $user['User']['id'];
			if ($this -> save($data)) {
				$retVal['response']['isSuccess'] = true;
				// We need to get some data to handle this event
				// $collectible = $this -> Collectible -> find('first', array('contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $data['CollectiblesWishLists']['collectible_id'])));
				// $this -> getEventManager() -> dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectible, 'stash' => $stash)));
				// // This is old school event, will be replaced by activity stuff later TODO
				// $this -> getEventManager() -> dispatch(new CakeEvent('Controller.Stash.Collectible.add', $this, array('collectibleUserId' => $this -> id, 'stashId' => $stash['Stash']['id'])));
			} else {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
				$retVal['response']['errors'] = $errors;
			}
		}

		return $retVal;
	}

}
?>