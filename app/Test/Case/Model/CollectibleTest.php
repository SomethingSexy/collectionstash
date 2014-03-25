<?php
App::uses('Collectible', 'Model');
App::import('Behavior', 'Editable');

class CollectibleTest extends CakeTestCase {
	// ugh these are all pretty much needed for collectible remove since it has to remove a lot of stuff
	public $fixtures = array('app.collectible', 'app.user', 'app.collectible_price_fact', 'app.listing', 'app.collectibles_user', 'app.collectibles_wishlist', 'app.collectibles_upload', 'app.attributes_collectible', 'app.artists_collectible', 'app.collectibles_tag', 'app.specialized_type', 'app.manufacture', 'app.collectibletype', 'app.license', 'app.scale', 'app.retailer', 'app.custom_status', 'app.status', 'app.entity_type', 'app.revision', 'app.series', 'app.currency', 'app.profile', 'app.wish_list', 'app.action', 'app.upload', 'app.attribute', 'app.collectibles_edit', 'app.collectibles_uploads_edit', 'app.attributes_collectibles_edit', 'app.artists_collectibles_edit', 'app.collectibles_tags_edit', 'app.edit', 'app.uploads_edit', 'app.comment', 'app.subscription', 'app.stash', 'app.latest_comment');

	public function setUp() {
		parent::setUp();
		$this -> Collectible = ClassRegistry::init('Collectible');
		$this -> Manufacture = ClassRegistry::init('Manufacture');
		$this -> License = ClassRegistry::init('License');
		$this -> Collectibletype = ClassRegistry::init('Collectibletype');
		$this -> Edit = ClassRegistry::init('Edit');
		$this -> CollectiblesUpload = ClassRegistry::init('CollectiblesUpload');
		$this -> AttributesCollectible = ClassRegistry::init('AttributesCollectible');
		$this -> ArtistsCollectible = ClassRegistry::init('ArtistsCollectible');
		$this -> CollectiblesTag = ClassRegistry::init('CollectiblesTag');
		$this -> EntityType = ClassRegistry::init('EntityType');
		$this -> Comment = ClassRegistry::init('Comment');
		$this -> CollectiblePriceFact = ClassRegistry::init('CollectiblePriceFact');
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

		$results = $this -> EntityType -> find('first', array('contain' => false, 'conditions' => array('EntityType.id' => 1)));
		$this -> assertEmpty($results);

		$results = $this -> Comment -> find('first', array('contain' => false, 'conditions' => array('Comment.entity_type_id' => 1)));
		$this -> assertEmpty($results);

		$results = $this -> CollectiblePriceFact -> find('first', array('contain' => false, 'conditions' => array('CollectiblePriceFact.id' => 1)));
		$this -> assertEmpty($results);
	}

	/**
	 * Testing admin remove of a collectible with edits
	 */
	public function testRemoveWithEdits() {
		$result = $this -> Collectible -> remove(2, array('User' => array('id' => 1, 'admin' => true)));
		$this -> assertEquals(true, $result['response']['isSuccess']);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 2)));
		$this -> assertEmpty($results);

		$results = $this -> Edit -> find('first', array('contain' => false, 'conditions' => array('Edit.id' => 1)));
		$this -> assertEmpty($results);

		$results = $this -> Collectible -> findEdit(1);
		$this -> assertEmpty($results);

		$results = $this -> Edit -> find('first', array('contain' => false, 'conditions' => array('Edit.id' => 2)));
		$this -> assertEmpty($results);

		$results = $this -> CollectiblesUpload -> findEdit(1);
		$this -> assertEmpty($results);

		$results = $this -> Edit -> find('first', array('contain' => false, 'conditions' => array('Edit.id' => 3)));
		$this -> assertEmpty($results);

		$results = $this -> AttributesCollectible -> findEdit(1);
		$this -> assertEmpty($results);

		$results = $this -> Edit -> find('first', array('contain' => false, 'conditions' => array('Edit.id' => 4)));
		$this -> assertEmpty($results);

		$results = $this -> ArtistsCollectible -> findEdit(1);
		$this -> assertEmpty($results);

		$results = $this -> Edit -> find('first', array('contain' => false, 'conditions' => array('Edit.id' => 5)));
		$this -> assertEmpty($results);

		$results = $this -> CollectiblesTag -> findEdit(1);
		$this -> assertEmpty($results);

	}

	/**
	 * This tests removing a collectible that has variants and makes sure that the variants are not attached to the main anymore
	 */
	public function testRemoveSingleWithVariant() {
		$result = $this -> Collectible -> remove(3, array('User' => array('id' => 1, 'admin' => true)));
		$this -> assertEquals(true, $result['response']['isSuccess']);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 3)));
		$this -> assertEmpty($results);
		$results = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => 4)));
		$this -> assertEquals(false, $results['Collectible']['variant']);
		$this -> assertEquals(0, $results['Collectible']['variant_collectible_id']);
	}

}
?>