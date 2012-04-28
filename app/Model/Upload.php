<?php
class Upload extends AppModel {
    var $name = 'Upload';
    var $actsAs = array('Editable' => array('type' => 'upload', 'model' => 'UploadEdit', 'behaviors' => array('FileUpload.FileUpload' => array('fileModel' => 'UploadEdit'))), 'Revision', 'FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');
    var $hasMany = array('Collectible');

    // [Upload] => Array
    //       (
    //           [0] => Array
    //               (
    //                   [id] => 69
    //                   [name] => bluecity_20.gif
    //                   [type] => image/gif
    //                   [size] => 2611
    //                   [created] => 2010-12-31 20:49:29
    //                   [modified] => 2010-12-31 20:49:29
    //                   [collectible_id] => 213
    //               )
    //
    //       )

    public function isValidUpload($uploadData) {
        $validUpload = false;
        debug($uploadData);
        if (isset($uploadData['Upload']) && !empty($uploadData['Upload']) && isset($uploadData['Upload']['0']) && !empty($uploadData['Upload']['0']) && isset($uploadData['Upload']['0']['file']) && !empty($uploadData['Upload']['0']['file']))
            if (count($uploadData['Upload']) == 1) {
                if ($uploadData['Upload']['0']['file']['name'] != '' || $uploadData['Upload']['0']['url'] != '') {
                    $validUpload = true;
                }
            }

        return $validUpload;
    }

    function getUpdateFields($uploadEditId, $includeChanges = false, $notes = null) {
        //Grab out edit collectible
        $uploadEditVersion = $this -> findEdit($uploadEditId);
        //reformat it for us, unsetting some stuff we do not need
        $uploadFields = array();

        if ($uploadEditVersion['UploadEdit']['action'] === 'A') {
            $uploadFields['Upload'] = $uploadEditVersion['UploadEdit'];
            unset($uploadFields['Upload']['id']);
            unset($uploadFields['Upload']['created']);
            unset($uploadFields['Upload']['modified']);
            $uploadFields['Revision']['action'] = 'A';
        } else {
            // $uploadFields['Upload.name'] = '\'' . $uploadEditVersion['UploadEdit']['name'] . '\'';
            // $uploadFields['Upload.edit_user_id'] = '\'' . $uploadEditVersion['UploadEdit']['edit_user_id'] . '\'';
            // $uploadFields['Upload.type'] = '\'' . $uploadEditVersion['UploadEdit']['type'] . '\'';
            // $uploadFields['Upload.size'] = '\'' . $uploadEditVersion['UploadEdit']['size'] . '\'';

            $uploadFields['Upload']['name'] = $uploadEditVersion['UploadEdit']['name'];
            // $uploadFields['Upload']['edit_user_id'] = '\'' . $uploadEditVersion['UploadEdit']['edit_user_id'] . '\'';
            $uploadFields['Upload']['type'] = $uploadEditVersion['UploadEdit']['type'];
            $uploadFields['Upload']['size'] = $uploadEditVersion['UploadEdit']['size'];
            $uploadFields['Upload']['id'] = $uploadEditVersion['UploadEdit']['base_id'];
            $uploadFields['Revision']['action'] = 'E';
        }

        if (!is_null($notes)) {
            $uploadFields['Revision']['notes'] = $notes;
        }
        //Make sure I grab the user id that did this edit
        $uploadFields['Revision']['user_id'] = $uploadEditVersion['UploadEdit']['edit_user_id'];

        return $uploadFields;
    }

}
?>           