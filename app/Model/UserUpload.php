<?php
class UserUpload extends AppModel
{
    var $name = 'UserUpload';
    var $belongsTo = array('User' => array('counterCache' => true));
    var $hasMany = array('CollectiblesUser');
    var $actsAs = array('FileUpload.FileUpload' => array('maxFileSize' => '2097152', 'uploadDirFunction' => 'userFilePath'), 'Containable');
    var $validate = array(
    //name field
    'title' => array('maxLength' => array('rule' => array('maxLength', 100), 'message' => 'Invalid length.'), 'alphaNumber' => array('rule' => array('custom', '/^[a-z0-9 ]*$/i'), 'message' => 'Must be alphanumeric.')),
    //description field
    'description' => array('maxLength' => array('rule' => array('maxLength', 150), 'message' => 'Invalid length.'), 'alphaNumber' => array('rule' => array('custom', '/^[a-z0-9 ]*$/i'), 'message' => 'Must be alphanumeric.')));
    
    function userFilePath($uploadDirectory) {
        //Logic for sanitizing your filename
        return WWW_ROOT . 'user_uploads' . DS . $this->data['UserUpload']['user_id'];
    }
    /**
     * TODO I really need to fix this in the upload plugin
     */
    public function isValidUpload($uploadData) {
        $validUpload = false;
        if (isset($uploadData['UserUpload']) && !empty($uploadData['UserUpload']) && isset($uploadData['UserUpload']['file']) && !empty($uploadData['UserUpload']['file'])) {
            if ($uploadData['UserUpload']['file']['name'] != '' || $uploadData['UserUpload']['url'] != '') {
                $validUpload = true;
            }
        }
        
        if (!$validUpload) {
            $this->validationErrors['file'] = 'Image is required.';
        }
        
        return $validUpload;
    }
}
?>