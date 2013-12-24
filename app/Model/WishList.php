<?php
class WishList extends AppModel {
	public $name = 'WishList';
	//public $useTable = 'wish_lists';
	public $hasMany = array('CollectiblesWishList' => array('dependent' => true));
	public $belongsTo = array('User');
	public $actsAs = array('Containable');

	public function getListOfUserWishlist($collectibleId) {
		$data = $this -> CollectiblesWishList -> find("all", array('conditions' => array('CollectiblesWishList.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'WishList'))));
		return $data;
	}

	public function getWishList($userId) {
		return $this -> find('first', array('contain' => false, 'conditions' => array('WishList.user_id' => $userId)));
	}

}
?>
