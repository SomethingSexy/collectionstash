<?php
App::uses('Profile', 'Model');
class ProfileTest extends CakeTestCase
{
    
    public $fixtures = array('app.user', 'app.profile');
    
    public function setUp() {
        parent::setUp();
        $this->Profile = ClassRegistry::init('Profile');
        $this->User = ClassRegistry::init('User');
    }
    
    /**
     * Testing admin remove of a single collectible
     */
    public function testUpdateProfile() {
        $result = $this->Profile->updateProfile(array('first_name' => 'Zach', 'last_name' => 'Dude', 'email' => 'tyler.cvetan@gmail.com'), array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], true);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
        
        $this->assertEqual($user['User']['first_name'], 'Zach');
        $this->assertEqual($user['User']['last_name'], 'Dude');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');
    }
    
    public function testUpdateProfileChangeEmail() {
        $result = $this->Profile->updateProfile(array('first_name' => 'Tyler', 'last_name' => 'Cvetan', 'email' => 'tyler.forum@gmail.com'), array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], true);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.forum@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');
    }
    
    public function testUpdateProfileChangeEmailDup() {
        $result = $this->Profile->updateProfile(array('first_name' => 'Tyler', 'last_name' => 'Cvetan', 'email' => 'admin@collectionstash.com'), array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], false);
        $this->assertEqual(empty($result['response']['data']['email']), false);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');
    }
}
?>