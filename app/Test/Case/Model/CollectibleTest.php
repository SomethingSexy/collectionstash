<?php
App::uses('Collectible', 'Model');
App::import('Behavior', 'Editable');

class CollectibleTest extends CakeTestCase {
	// ugh these are all pretty much needed for collectible remove since it has to remove a lot of stuff
	public $fixtures = array('app.collectible', 'app.user', 'app.collectible_price_fact', 'app.listing', 'app.collectibles_user', 'app.collectibles_wishlist', 'app.collectibles_upload', 'app.attributes_collectible', 'app.artists_collectible', 'app.collectibles_tag', 'app.specialized_type', 'app.manufacture', 'app.collectibletype', 'app.license', 'app.scale', 'app.retailer', 'app.custom_status', 'app.status', 'app.entity_type', 'app.revision', 'app.series', 'app.currency', 'app.profile', 'app.wish_list', 'app.action', 'app.upload', 'app.attribute', 'app.collectibles_edit', 'app.collectibles_uploads_edit', 'app.attributes_collectibles_edit', 'app.artists_collectibles_edit', 'app.collectibles_tags_edit', 'app.edit', 'app.uploads_edit', 'app.comment', 'app.subscription', 'app.stash');

	public function setUp() {
		parent::setUp();
		$this -> Collectible = ClassRegistry::init('Collectible');
		$this -> Manufacture = ClassRegistry::init('Manufacture');
		$this -> License = ClassRegistry::init('License');
		$this -> Collectibletype = ClassRegistry::init('Collectibletype');
	}

	/**
	 * Testing admin remove of a single collectible
	 */
	public function testRemoveSingle() {
		$result = $this -> Collectible -> remove(1, array('User' => array('id' => 1, 'admin' => true)));
		$this -> assertEquals(true, $result['response']['isSuccess']);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 1)));
		$this -> assertEmpty($results);
		// make sure manufacturer, license and collectibletype don't get deleted
		$results = $this -> Manufacture -> find('first', array('contain' => false, 'conditions' => array('Manufacture.id' => 1)));
		$this -> assertNotEmpty($results);
		$results = $this -> License -> find('first', array('contain' => false, 'conditions' => array('License.id' => 1)));
		$this -> assertNotEmpty($results);
		$results = $this -> Collectibletype -> find('first', array('contain' => false, 'conditions' => array('Collectibletype.id' => 1)));
		$this -> assertNotEmpty($results);
	}

	/**
	 * Testing admin remove of a single collectible
	 */
	public function testRemoveWithEdits() {
		$result = $this -> Collectible -> remove(2, array('User' => array('id' => 1, 'admin' => true)));
		$this -> assertEquals(true, $result['response']['isSuccess']);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 2)));
		$this -> assertEmpty($results);

	}

}
?>