<?php
class Profile extends AppModel
{
    public $name = 'Profile';
    public $belongsTo = array('User');
    public $actsAs = array('Containable');
    public $validate = array(
    // display name
    'display_name' => array(
    //valid values
    'validValues' => array('rule' => '/^[a-z0-9 -,.]*$/i', 'required' => false, 'allowEmpty' => true, 'message' => 'Invalid characters.'),
    //valid length
    'validLength' => array('rule' => array('maxLength', 100), 'message' => 'Maximum 100 characters long')),
    //location
    'location' => array(
    //valid values
    'validValues' => array('rule' => '/^[a-z0-9 -,.]*$/i', 'required' => false, 'allowEmpty' => true, 'message' => 'Invalid characters.'),
    //valid length
    'validLength' => array('rule' => array('maxLength', 100), 'message' => 'Maximum 100 characters long')));
    /**
     * This will handle update the user profile, which might contain user model data as well
     */
    public function updateProfile($data, $user) {
        $retVal = $this->buildDefaultResponse();
        // make sure there is no hacking
        unset($data['id']);
        $userFields = array('first_name', 'last_name', 'modified');
        $profileFields = array('email_newsletter', 'email_notification', 'modified', 'display_name', 'location');
        $activeUserModel = $this->User->find('first', array('conditions' => array('User.id' => $user['User']['id']), 'contain' => array('Profile')));
        // if it is a change then we need to validate and verify it is not a dup
        if (isset($data['email']) && $data['email'] !== $activeUserModel['User']['email']) {
            array_push($userFields, 'email');
        }
        
        $data = array('User' => $data, 'Profile' => $data);
        $data['User']['id'] = $user['User']['id'];
        $data['Profile']['id'] = $activeUserModel['Profile']['id'];
        // using saveAssociated should help with transactional issues and validation
        if (!$this->User->saveAssociated($data, array('fieldList' => array('User' => $userFields, 'Profile' => $profileFields)))) {
            $retVal['response']['data'] = $this->User->validationErrors;
            return $retVal;
        }
        
        $retVal['response']['data'] = $data;
        $retVal['response']['isSuccess'] = true;
        
        return $retVal;
    }
}
?>
