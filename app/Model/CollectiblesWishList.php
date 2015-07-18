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
				$collectible = $this -> Collectible -> find('first', array('contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $data['CollectiblesWishList']['collectible_id'])));
				$this -> getEventManager() -> dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COLLECTIBLE_WISH_LIST, 'user' => $user, 'collectible' => $collectible, 'wishlist' => $wishList)));
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.WishList.Collectible.add', $this, array('collectibleWishListId' => $this -> id, 'wishListId' => $wishList['WishList']['id'])));
			} else {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
				$retVal['response']['errors'] = $errors;
			}
		}

		return $retVal;
	}

	public function remove($data, $user) {
		$retVal = $this -> buildDefaultResponse();
		// grab the collectible we are removing first, needed for the event
		$collectiblesUser = $this -> find("first", array('conditions' => array('CollectiblesWishList.id' => array($data['CollectiblesWishList']['id'])), 'contain' => array('User', 'Collectible', 'WishList')));

		// we need to check permissions first
		// return 401 if they are not allowed to edit this one
		if (!$this -> isEditPermission($collectiblesUser, $user)) {
			$retVal['response']['code'] = 401;

			return $retVal;
		}

		// just remove it completely
		if ($this -> delete($data['CollectiblesWishList']['id'])) {
			$retVal['response']['isSuccess'] = true;
			$this -> getEventManager() -> dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_WISH_LIST, 'user' => $user, 'collectible' => $collectiblesUser, 'wishlist' => $collectiblesUser)));
		}

		return $retVal;
	}

	public function isEditPermission($check, $user) {
		$retVal = false;

		// setup to work for when we have the collectible object
		// already or just the id
		if (is_numeric($check) || is_string($check)) {
			$collectible = $this -> find('first', array('conditions' => array('CollectiblesWishList.id' => $check), 'contain' => false));
			//lol
		} else {
			// assume object
			$collectible = $check;
		}

		// they must be the current owner of this collectible to edit it
		if (!empty($collectible) && $collectible['CollectiblesWishList']['user_id'] === $user['User']['id']) {
			$retVal = true;
		}

		return $retVal;
	}

}
?>