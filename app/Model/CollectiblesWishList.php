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

}
?>