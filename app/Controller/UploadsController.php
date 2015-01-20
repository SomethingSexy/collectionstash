<?php
class UploadsController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify');
    public $components = array('Image');
    
    public function add() {
        $this->autoRender = false;
        // you have to be logged in to upload a file.
        // otherwise, we aren't stopping people from uploading stuff
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            debug($this->request->data);
            $response = $this->Upload->add($this->request->data, $this->getUserId());
            if ($response) {
                if ($response['response']['isSuccess']) {
                    debug($response);
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
                    $uploadResponse['delete_url'] = '/uploads/remove/' . $upload['Upload']['id'];
                    
                    $uploadResponse['delete_type'] = 'DELETE';
                    $uploadResponse['id'] = $upload['Upload']['id'];
                    $uploadResponse['allowDelete'] = true;
                    
                    array_push($retunData['files'], $uploadResponse);
                    $this->response->body(json_encode($retunData));
                } else {
                    if ($response['response']['code'] === 401) {
                        $this->response->statusCode(401);
                    } else if ($response['response']['code'] === 400) {
                        $this->response->statusCode(400);
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
    // only allow the upload to be removed, if the user is an admin
    // and it isn't tied to anything.  Otherwise, collectible photo deletes
    // and edits go through collectiblesupload
    public function remove($id) {
        $this->autoRender = false;
        // you have to at least be logged in to remove a file
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isDelete()) {
            $allowDelete = false;
            if ($this->isUserAdmin()) {
                $allowDelete = true;
            } else {
                // if an entity needs a more fine grain solution
                // then it show implement its own delete
                // check to see if it is tied to
                //AttributesUpload
                //CollectiblesUpload
                //Manufacturer
                //AttributesUploadEdits
                //CollectiblesUploadEdits
                
                if ($this->Upload->AttributesUpload->find('count', array('conditions' => array('AttributesUpload.upload_id' => $id))) > 0) {
                    $allowDelete = false;
                } else if ($this->Upload->CollectiblesUpload->find('count', array('conditions' => array('CollectiblesUpload.upload_id' => $id))) > 0) {
                    $allowDelete = false;
                } else if ($this->Upload->Manufacturer->find('count', array('conditions' => array('Manufacturer.upload_id' => $id))) > 0) {
                    $allowDelete = false;
                } else {
                    $attrEdits = $this->Upload->AttributesUpload->findEdits(array('conditions' => array('AttributesUploadEdit.upload_id' => $id)));
                    $colEdits = $this->Upload->CollectiblesUpload->findEdits(array('conditions' => array('CollectiblesUploadEdit.upload_id' => $id)));
                    
                    if (!empty($edits) || !empty($colEdits)) {
                        $allowDelete = false;
                    } else {
                        $allowDelete = true;
                    }
                }
            }
            
            if ($allowDelete) {
                if ($this->Upload->delete($id)) {
                    $this->response->body('{}');
                } else {
                    $this->response->statusCode(500);
                }
            }
        } else {
            $this->response->statusCode(405);
        }
    }
}
?>
