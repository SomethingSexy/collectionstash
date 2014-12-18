<?php
App::uses('Sanitize', 'Utility');
class AttributesUploadsController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify', 'CollectibleDetail');
    
    public $components = array('Image');
    /**
     * This will submit a delete edit OR if it is pending and the user logged in
     * is the one deleting it, it will automatically delete it
     */
    public function remove($id) {
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to remove a photo.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        
        $upload = array();
        $upload['AttributesUpload']['id'] = $id;
        $response = $this->AttributesUpload->remove($upload, $this->getUser());
        if ($response) {
            if ($response['response']['isSuccess']) {
                $retunData = array();
                
                $this->set('returnData', $retunData);
            } else {
                // Need to figure out how the plugin handles errors
                
                
            }
        } else {
            //Something really fucked up
            $data['isSuccess'] = false;
            $data['errors'] = array('message', __('Invalid request.'));
            $this->set('returnData', $data);
        }
    }
    /**
     * TODO: this needs to return an AttributesUpload -> Upload object
     */
    public function upload() {
        $this->layout = 'ajax';
        $data = array();
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to add a photo.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $response = $this->AttributesUpload->add($this->request->data, $this->getUser());
            if ($response) {
                if ($response['response']['isSuccess']) {
                    $upload = $response['response']['data'];
                    // We need to return the data in a specific format for the upload plugin to handle
                    // it propertly
                    $resizedImg = $this->Image->image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                    $img = $this->Image->image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
                    $retunData = array();
                    $retunData['files'] = array();
                    $uploadResponse = array();
                    $uploadResponse['url'] = $img['path'];
                    $uploadResponse['thumbnail_url'] = $resizedImg['path'];
                    $uploadResponse['type'] = $upload['Upload']['type'];
                    $uploadResponse['size'] = intval($upload['Upload']['size']);
                    $uploadResponse['name'] = $upload['Upload']['name'];
                    // this should be the id of the new pending collectible
                    $uploadResponse['delete_url'] = '/attributes_uploads/remove/' . $upload['AttributesUpload']['id'];
                    
                    $uploadResponse['delete_type'] = 'POST';
                    $uploadResponse['id'] = $this->request->data['AttributesUpload']['attribute_id'];
                    $uploadResponse['allowDelete'] = true;
                    
                    array_push($retunData['files'], $uploadResponse);
                    $this->set('returnData', $retunData);
                } else {
                    if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                        $this->response->statusCode(401);
                    } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                        $this->response->statusCode(400);
                        $this->response->body(json_encode($response['response']['data']));
                    } else {
                        $this->response->body(json_encode($response['response']['data']));
                    }
                }
            } else {
                //Something really fucked up
                $data['isSuccess'] = false;
                $data['errors'] = array('message', __('Invalid request.'));
                $this->set('returnData', $data);
            }
        } else {
            $data['isSuccess'] = false;
            $data['errors'] = array('message', __('Invalid request.'));
            $this->set('returnData', $data);
            return;
        }
    }
    
    public function uploads($partId) {
        $this->autoRender = false;
        $part = array();
        
        if (isset($partId) && is_numeric($partId)) {
            $part = $this->AttributesUpload->Attribute->find('first', array('contain' => array('AttributesUpload' => array('Upload')), 'conditions' => array('Attribute.id' => $partId)));
            
            $part = $part['AttributesUpload'];
            // // we have to do some processing on the part uploads, kind of lame
            
            foreach ($part as $key => $value) {
                $thumbnail = $this->Image->image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                $part[$key]['Upload']['thumbnail_url'] = $thumbnail['path'];
                $part[$key]['Upload']['delete_url'] = '/attributes_uploads/remove/' . $value['id'] . '/false';
                $part[$key]['Upload']['delete_type'] = 'POST';
                $part[$key]['Upload']['pending'] = false;
                $part[$key]['Upload']['allowDelete'] = true;
            }
        }
        
        $this->response->body(json_encode($part));
    }
    
    public function admin_approval($editId = null, $collectibleUploadEditId = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($editId && is_numeric($editId) && $collectibleUploadEditId && is_numeric($collectibleUploadEditId)) {
            $this->set('collectibleUploadEditId', $collectibleUploadEditId);
            $this->set('editId', $editId);
            if (empty($this->request->data)) {
                $collectibleUpload = $this->AttributesUpload->getEditForApproval($collectibleUploadEditId);
                
                if ($collectibleUpload) {
                    $this->set(compact('collectibleUpload'));
                } else {
                    //uh fuck you
                    $this->redirect('/');
                }
            }
        } else {
            $this->redirect('/');
        }
    }
}
?>