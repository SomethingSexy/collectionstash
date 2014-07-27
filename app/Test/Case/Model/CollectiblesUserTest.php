<?php
App::uses('CollectiblesUser', 'Model');
class CollectiblesUserTest extends CakeTestCase
{
    
    // ugh these are all pretty much needed for collectible remove since it has to remove a lot of stuff
    public $fixtures = array('app.collectible', 'app.user', 'app.listing', 'app.collectibles_user', 'app.merchant', 'app.collectible_user_remove_reason', 'app.condition', 'app.transaction', 'app.stash');
    
    public function setUp() {
        parent::setUp();
        $this->CollectiblesUser = ClassRegistry::init('CollectiblesUser');
    }
    
    /**
     * Testing admin remove of a single collectible
     */
    public function testFind() {
        $result = $this->CollectiblesUser->find('first', array('conditions' => array('CollectiblesUser.id' => 1), 'contain' => array('Condition', 'Merchant', 'User', 'Stash', 'Listing')));
        $this->assertNotEmpty($result);
        
        $this->assertNotEmpty($result['Listing']);
        $this->assertNotEmpty($result['Listing']['id']);
        $this->assertEqual($result['Listing']['listing_price'], '10.00');
    }
}
?>