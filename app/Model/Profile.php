<?php
class Profile extends AppModel
{
    public $name = 'Profile';
    public $belongsTo = array('User');
    public $actsAs = array('Containable');
    
    /**
     * This will handle update the user profile, which might contain user model data as well
     */
    public function updateProfile($data, $user) {
        $retVal = $this->buildDefaultResponse();
        
        // make sure there is no hacking
        $data['id'] = $user['User']['id'];
        $fields = array('first_name', 'last_name', 'modified');
        $activeUserModel = $this->User->find('first', array('conditions' => array('User.id' => $user['User']['id']), 'contain' => false));
        
        // if it is a change then we need to validate and verify it is not a dup
        if ($data['email'] !== $activeUserModel['User']['email']) {
            array_push($fields, 'email');
        }
        
        $this->User->id = $user['User']['id'];
        if (!$this->User->save($data, true, $fields)) {
            $retVal['response']['data'] = $this->User->validationErrors;
            return $retVal;
        }
        
        // $this->id = $data['id'];
        // $this->save($data, false, array('email_newsletter', 'email_notification', 'modified'));
        
        $retVal['response']['data'] = $data;
        $retVal['response']['isSuccess'] = true;
        
        return $retVal;
    }
}
?>
