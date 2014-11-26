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
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Zach');
        $this->assertEqual($user['User']['last_name'], 'Dude');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');
        $this->assertEqual($user['Profile']['email_notification'], true);
        $this->assertEqual($user['Profile']['email_newsletter'], true);
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

    public function testUpdateProfileInvalidName() {
        $result = $this->Profile->updateProfile(array('first_name' => 'Tyler<>', 'last_name' => 'Cvetan<>', 'email' => 'admin@collectionstash.com'), array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], false);
        $this->assertEqual(empty($result['response']['data']['first_name']), false);
        $this->assertEqual(empty($result['response']['data']['last_name']), false);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');
    }
    
    public function testUpdateProfileNotifications() {
        $result = $this->Profile->updateProfile(array('first_name' => 'Tyler', 'last_name' => 'Cvetan', 'email' => 'tyler.cvetan@gmail.com', 'email_notification' => false, 'email_newsletter' => false), array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], true);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');

        $this->assertEqual($user['Profile']['email_notification'], false);
        $this->assertEqual($user['Profile']['email_newsletter'], false);
    }

    public function testUpdateProfileDisplayName() {
    	$userData = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
    	$userData = $userData['User'];
    	unset($userData['id']);
    	$userData['display_name'] = 'Tyler Cvetan';

        $result = $this->Profile->updateProfile($userData, array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], true);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');

        $this->assertEqual($user['Profile']['email_notification'], true);
        $this->assertEqual($user['Profile']['email_newsletter'], true);
        $this->assertEqual($user['Profile']['display_name'], 'Tyler Cvetan');
    }

    public function testUpdateProfileDisplayNameInvalid() {
    	$userData = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
    	$userData = $userData['User'];
    	unset($userData['id']);
    	$userData['display_name'] = 'Tyler <>Cvetan';

        $result = $this->Profile->updateProfile($userData, array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], false);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');

        $this->assertEqual($user['Profile']['email_notification'], true);
        $this->assertEqual($user['Profile']['email_newsletter'], true);
        $this->assertEqual($user['Profile']['display_name'], null);
    }

    // for this test we are changing the user model and the profile model, the profile model fails, we want to role back user changes
    public function testUpdateProfileInvalidProfileModel() {
    	$userData = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
    	$userData = $userData['User'];
    	unset($userData['id']);
    	$userData['first_name'] = 'butthole';
    	$userData['display_name'] = 'Tyler <>Cvetan';

        $result = $this->Profile->updateProfile($userData, array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], false);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');

        $this->assertEqual($user['Profile']['email_notification'], true);
        $this->assertEqual($user['Profile']['email_newsletter'], true);
        $this->assertEqual($user['Profile']['display_name'], null);
    }

    public function testUpdateProfileLocation() {
    	$userData = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => false));
    	$userData = $userData['User'];
    	unset($userData['id']);
    	$userData['location'] = 'Milwaukee, Wisconsin';

        $result = $this->Profile->updateProfile($userData, array('User' => array('id' => 2)));
        
        $this->assertNotEmpty($result);
        
        $this->assertEqual($result['response']['isSuccess'], true);
        
        $user = $this->User->find('first', array('conditions' => array('User.id' => 2), 'contain' => array('Profile')));
        
        $this->assertEqual($user['User']['first_name'], 'Tyler');
        $this->assertEqual($user['User']['last_name'], 'Cvetan');
        $this->assertEqual($user['User']['email'], 'tyler.cvetan@gmail.com');
        $this->assertEqual($user['User']['username'], 'Balls');

        $this->assertEqual($user['Profile']['email_notification'], true);
        $this->assertEqual($user['Profile']['email_newsletter'], true);
        $this->assertEqual($user['Profile']['location'], 'Milwaukee, Wisconsin');
    }
}
?>