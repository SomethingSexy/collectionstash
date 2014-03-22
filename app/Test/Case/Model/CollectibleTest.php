<?php
App::uses('Collectible', 'Model');

class CollectibleTest extends CakeTestCase {
	public $fixtures = array('app.collectible', 'app.user', 'app.collectible_price_fact', 'app.listing', 'app.collectibles_user', 'app.collectibles_wishlist', 'app.collectibles_upload', 'app.attributes_collectible', 'app.artists_collectible', 'app.collectibles_tag', 'app.specialized_type');

	public function setUp() {
		parent::setUp();
		$this -> Collectible = ClassRegistry::init('Collectible');
	}
	
	/**
	 * Testing admin remove of a single collectible
	 */
	public function testRemoveSingle() {
		$result = $this -> Collectible -> remove(1, array('User' => array('id' => 1, 'admin' => true)));
		$this -> assertEquals(true, $result['response']['isSuccess']);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 1)));

		$this -> assertEmpty($results);
	}

}
?>