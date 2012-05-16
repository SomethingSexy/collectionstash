<?php
App::uses('Sanitize', 'Utility');
class UploadEditsController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');

	//TODO this should get moved to an upload edit
	function edit($collectibleId = null, $id = null) {
		$this -> checkLogIn();
		//First time in or a refresh
		if (empty($this -> request -> data)) {
			//if collectible id is null and we didn't add it to the session already someone fucked up, redirect them
			if (!is_null($collectibleId)) {
				//If it is not null, then see if it was added to the session, if it was replace it, otherwise write it to the session
				if ($this -> Session -> check('Upload.Edit.collectibleId')) {
					$currentCollectibleId = $this -> Session -> read('Upload.Edit.collectibleId');
					if ($currentCollectibleId !== $collectibleId) {
						$this -> Session -> write('Upload.Edit.collectibleId', $collectibleId);
					}
				} else {
					$this -> Session -> write('Upload.Edit.collectibleId', $collectibleId);
				}
			} else if (!$this -> Session -> check('Upload.Edit.collectibleId')) {
				$this -> redirect('/');
			}
			//Make sure the id is not null
			if (!is_null($id)) {
				//See if we already came in here
				if ($this -> Session -> check('Upload.Edit.upload')) {
					//if we did, check to see if the id in the session is the same as the one being passed in
					$upload = $this -> Session -> read('Upload.Edit.upload');
					if ($upload['Upload']['id'] === $id) {
						$this -> set('collectible', $upload);
					} else {
						//if not, reload it
						$this -> loadModel('Upload');
						$upload = $this -> Upload -> find('first', array('conditions' => ( array('Upload.id' => $id)), 'contain' => false));
						$collectible = array();
						$collectible = $upload;
						debug($collectible);
						$this -> Session -> write('Upload.Edit.upload', $collectible);
						$this -> set('collectible', $collectible);
					}
				} else {
					//If it does not exist in the session, lets load that bitch
					$this -> loadModel('Upload');
					$upload = $this -> Upload -> find('first', array('conditions' => ( array('Upload.id' => $id)), 'contain' => false));
					$collectible = array();
					$collectible = $upload;
					debug($collectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> write('Upload.Edit.upload', $collectible);
				}

			} else {
				//Let's delete just to be safe
				$this -> Session -> delete('Upload.Edit.upload');
				//If it is null and the data is empty that means there is no image
				//TODO handle this scenario
			}
			$this -> set('collectibleId', $this -> Session -> read('Upload.Edit.collectibleId'));
			$this -> set('addImage', !$this -> Session -> check('Upload.Edit.upload'));

		} else {
			/*
			 * Ok we are submitting something, first check to the session to see if we added
			 * an upload
			 */
			$this -> set('collectibleId', $this -> Session -> read('Upload.Edit.collectibleId'));
			$this -> set('addImage', !$this -> Session -> check('Upload.Edit.upload'));
			$this -> set('collectible', $this -> Session -> read('Upload.Edit.upload'));

			$this -> loadModel('Upload');

			if ($this -> Upload -> isValidUpload($this -> request -> data)) {
				$currentCollectibleId = $this -> Session -> read('Upload.Edit.collectibleId');
				$this -> request -> data['Upload'][0]['collectible_id'] = $currentCollectibleId;
				$this -> request -> data['Upload'][0]['edit_user_id'] = $this -> getUserId();
				if ($this -> Session -> check('Upload.Edit.upload')) {
					//If we have an image we are replacing, lets grab that uploadId, so we know which one is being replaced.
					$originalUpload = $this -> Session -> read('Upload.Edit.upload');
					$this -> request -> data['Upload'][0]['base_id'] = $originalUpload['Upload']['id'];
					//Set the action to "R" to signify it is being replaces
					$this -> request -> data['Upload'][0]['action'] = 'E';
					debug($this -> request -> data);
				} else {
					//This means there was no image originally, add a "N" for new
					$this -> request -> data['Upload'][0]['action'] = 'A';
				}

				//Since you can only update one at a time, we will have to do this here
				$this -> request -> data['Upload'] = $this -> request -> data['Upload'][0];

				$returnData = $this -> Upload -> saveEdit($this -> request -> data, $originalUpload['Upload']['id'], $this -> getUserId(), $this -> Session -> read('Upload.Edit.collectibleId'));
				debug($returnData);
				if ($returnData) {
					$this -> set('collectible', $returnData);

					$this -> Session -> delete('Upload.Edit.collectibleId');
					$this -> Session -> delete('Upload.Edit.upload');
					$this -> render('confirm');
					return;
				} else {
					debug($this -> UploadEdit -> validationErrors);
					$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
				}
			} else {
				$this -> Upload -> validationErrors['0']['file'] = 'Image is required.';
				debug($this -> Upload -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
		}

	}

	function admin_approval($editId = null, $uploadEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $uploadEditId && is_numeric($uploadEditId)) {
			$this -> set('uploadEditId', $uploadEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$this -> loadModel('Upload');
				$uploadEditVersion = $this -> Upload -> findEdit($uploadEditId);
				if (!empty($uploadEditVersion)) {
					$upload = array();
					$upload['Upload'] = $uploadEditVersion['UploadEdit'];
					$this -> set('upload', $upload);
				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}

		} else {
			$this -> redirect('/');
		}
	}

}
?>