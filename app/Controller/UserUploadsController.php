<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class UserUploadsController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify', 'Time');
    
    public $components = array('Image');
    // this will be used to retrieve user images via ajax
    public function index($username = null) {
        $this->set(compact('username'));
        if ($this->request->isGet()) {
            $user = $this->UserUpload->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
            $this->paginate = array('paramType' => 'querystring', 'limit' => 25, 'conditions' => array('UserUpload.user_id' => $user['User']['id']), 'contain' => false);
            $userUploads = $this->paginate('UserUpload');
            
            foreach ($userUploads as $key => $value) {
                debug($value);
                $img = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $user['User']['id'], 'imagePathOnly' => true));
                $resizedImg = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $user['User']['id'], 'width' => 200, 'height' => 200, 'imagePathOnly' => true, 'resizeType' => 'adaptive'));
                $largeThumbnail = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $user['User']['id'], 'width' => 400, 'height' => 400, 'imagePathOnly' => true, 'resizeType' => 'adaptive'));
                $userUploads[$key]['UserUpload']['imagePath'] = $img['path'];
                $userUploads[$key]['UserUpload']['resizedImagePath'] = $resizedImg['path'];
                $userUploads[$key]['UserUpload']['thumbnail_url'] = $largeThumbnail['path'];
            }
            
            $userUploads = Set::extract('/UserUpload/.', $userUploads);
            
            $this->set('uploads', $userUploads);
        }
    }
    /**
     * Ignoring $userId for now, this will go after the userid in the session since you can't
     * edit other people's photos, that would be ridiclous.
     *
     * This will also just retrieve the uploads we will be using other actions to update
     */
    public function edit($userId = null) {
        $this->checkLogIn();
        $userId = $this->getUserId();
        $userUploadsFull = $this->UserUpload->find('all', array('contain' => array('User'), 'conditions' => array('UserUpload.user_id' => $userId)));
        $userUploads = array();
        foreach ($userUploadsFull as $key => $value) {
            $img = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id'], 'imagePathOnly' => true));
            $resizedImg = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id'], 'width' => 400, 'height' => 400, 'imagePathOnly' => true, 'resizeType' => 'adaptive'));
            $userUploadsFull[$key]['UserUpload']['imagePath'] = $img['path'];
            $userUploadsFull[$key]['UserUpload']['resizedImagePath'] = $resizedImg['path'];
            $userUploadsFull[$key]['UserUpload']['externalImagePath'] = 'http://' . env('SERVER_NAME') . $img['path'];
            // clean this up to make it easier for the UI to use
            array_push($userUploads, $userUploadsFull[$key]['UserUpload']);
        }
        
        $this->set(compact('userUploads'));
    }
    /**
     * This will display all uploads for the user logged in, since this is not a publically visible page
     * to share photos, we are just going to use who is logged in at the time. Might want to change this later
     * depending on how this page grows.
     */
    public function uploads() {
        $this->checkLogIn();
        if (Configure::read('Settings.User.uploads.allowed')) {
            $uploads = $this->UserUpload->find('all', array('conditions' => array('UserUpload.user_id' => $this->getUserId())));
            $returnData = array();
            $returnData['files'] = array();
            foreach ($uploads as $key => $value) {
                $resizedImg = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id'], 'width' => 100, 'height' => 200, 'imagePathOnly' => true, 'resizeType' => 'adaptive'));
                $img = $this->Image->image($value['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id'], 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
                $uploadResponse = array();
                $uploadResponse['url'] = $img['path'];
                $uploadResponse['thumbnailUrl'] = $resizedImg['path'];
                $uploadResponse['type'] = $value['UserUpload']['type'];
                $uploadResponse['size'] = intval($value['UserUpload']['size']);
                $uploadResponse['name'] = $value['UserUpload']['name'];
                $uploadResponse['deleteUrl'] = '/user_uploads/remove/' . $value['UserUpload']['id'];
                $uploadResponse['edit_url'] = '/user_uploads/upload/' . $value['UserUpload']['id'];
                $uploadResponse['deleteType'] = 'POST';
                
                array_push($returnData['files'], $uploadResponse);
            }
            //This is fucking bullshit but counter cache is not working right now
            $uploadCount = $this->UserUpload->find('count', array('conditions' => array('UserUpload.user_id' => $this->getUserId())));
            $this->set('uploads', $returnData);
            $this->set(compact('uploadCount'));
            $this->set('username', $this->getUsername());
        } else {
            $this->viewPath = 'errors';
            $this->render('invalid_request');
        }
    }
    
    public function upload($id) {
        
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPut()) {
            $this->UserUpload->id = $id;
            $data = $this->request->input('json_decode', true);
            if (!$this->UserUpload->save($data, true, array('title', 'description'))) {
                $this->response->statusCode(400);
                $this->set('returnData', array('errors' => $this->convertErrorsJSON($this->UserUpload->validationErrors)));
            }
        }
    }
    /**
     * This is an Ajax enabled action to upload photos for the user currently logged in.  Just need to check
     * if they are logged in here and nothing else since we are using the session data
     */
    public function add() {
        $data = array();
        $this->layout = 'ajax';
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to add an item.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $uploadCount = $this->UserUpload->find('count', array('conditions' => array('UserUpload.user_id' => $this->getUserId())));
            if ($uploadCount < Configure::read('Settings.User.uploads.total-allowed')) {
                if ($this->UserUpload->isValidUpload($this->request->data)) {
                    $this->request->data['UserUpload']['user_id'] = $this->getUserId();
                    if ($this->UserUpload->saveAll($this->request->data['UserUpload'])) {
                        //Grab the user id of the upload that was just added
                        $id = $this->UserUpload->id;
                        $upload = $this->UserUpload->findById($id);
                        $resizedImg = $this->Image->image($upload['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'], 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                        $img = $this->Image->image($upload['UserUpload']['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'], 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
                        $retunData = array();
                        $retunData['files'] = array();
                        $uploadResponse = array();
                        $uploadResponse['url'] = $img['path'];
                        $uploadResponse['thumbnailUrl'] = $resizedImg['path'];
                        $uploadResponse['type'] = $upload['UserUpload']['type'];
                        $uploadResponse['size'] = intval($upload['UserUpload']['size']);
                        $uploadResponse['name'] = $upload['UserUpload']['name'];
                        $uploadResponse['deleteUrl'] = '/user_uploads/remove/' . $upload['UserUpload']['id'];
                        $uploadResponse['deleteType'] = 'POST';
                        $uploadResponse['edit_url'] = '/user_uploads/upload/' . $upload['UserUpload']['id'];
                        array_push($retunData['files'], $uploadResponse);
                        $this->set('returnData', $retunData);
                        $this->getEventManager()->dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_ADD_PHOTO, 'user' => $this->getUser(), 'photo' => $upload)));
                    } else {
                        $retunData = array();
                        $uploadResponse = array();
                        $uploadResponse['error'] = $this->UserUpload->validationErrors;
                        array_push($retunData, $uploadResponse);
                        $this->set('returnData', $retunData);
                    }
                } else {
                    $retunData = array();
                    $uploadResponse = array();
                    $uploadResponse['error'] = $this->UserUpload->validationErrors;
                    array_push($retunData, $uploadResponse);
                    $this->set('returnData', $retunData);
                }
            } else {
                $retunData = array();
                $uploadResponse = array();
                $uploadResponse['error'] = __('You have reached your upload limit');
                array_push($retunData, $uploadResponse);
                $this->set('returnData', $retunData);
            }
        } else {
            $retunData = array();
            $uploadResponse = array();
            $uploadResponse['error'] = __('Invalid Request');
            array_push($retunData, $uploadResponse);
            $this->set('returnData', $retunData);
        }
    }
    
    public function remove($id) {
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to add an item.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        if (!empty($id)) {
            $userUpload = $this->UserUpload->findById($id);
            if (!empty($userUpload)) {
                if ($userUpload['UserUpload']['user_id'] === $this->getUserId()) {
                    $this->UserUpload->id = $userUpload['UserUpload']['id'];
                    if ($this->UserUpload->delete($userUpload['UserUpload']['id'])) {
                        // if removing, update all references to this userupload as well
                        $this->UserUpload->CollectiblesUser->updateAll(array('CollectiblesUser.user_upload_id' => null), array('CollectiblesUser.user_upload_id' => $userUpload['UserUpload']['id']));
                        $retunData = array();
                        $uploadResponse = array();
                        array_push($retunData, $uploadResponse);
                        $this->set('returnData', $retunData);
                    } else {
                        $retunData = array();
                        $uploadResponse = array();
                        $uploadResponse['error'] = $this->UserUpload->validationErrors;
                        
                        $this->set('returnData', $retunData);
                    }
                } else {
                    $retunData = array();
                    $uploadResponse = array();
                    $uploadResponse['error'] = __('Invalid Request');
                    array_push($retunData, $uploadResponse);
                    $this->set('returnData', $retunData);
                }
            } else {
                $retunData = array();
                $uploadResponse = array();
                $uploadResponse['error'] = __('Invalid Request');
                array_push($retunData, $uploadResponse);
                $this->set('returnData', $retunData);
            }
        } else {
            $retunData = array();
            $uploadResponse = array();
            $uploadResponse['error'] = __('Invalid Request');
            array_push($retunData, $uploadResponse);
            $this->set('returnData', $retunData);
        }
    }
    
    public function gallery() {
        // TODO: Going to need a privacy setting for uploads, that is separate from the stash
        // because when I allow for more stashes, it won't make sense
        $this->paginate = array('limit' => 50, 'order' => array('created' => 'desc'), 'contain' => array('User'));
        $userUploads = $this->paginate('UserUpload');
        $this->set(compact('userUploads'));
        $this->layout = 'fluid';
    }
}
?>
