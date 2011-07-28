<?php
class AttributesCollectiblesController extends AppController {

	var $name = 'AttributesCollectibles';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	function edit($id =null) {
		$this -> checkLogIn();
		$this -> set('collectibleId', $id);
		if(!empty($this -> data)) {
			debug($this -> data);
			if(isset($this -> data['AttributesCollectible'])) {
				$isValid = true;
				//TODO this does not seem right
				foreach($this -> data['AttributesCollectible'] as $key => $attribue) {
					$this -> AttributesCollectible -> set($attribue);
					//debug($this -> AttributesCollectible);
					if($this -> AttributesCollectible -> validates()) {

					} else {
						//If one is invalid set it to false
						$isValid = false;
						debug($this -> AttributesCollectible -> invalidFields());
						$this -> set('errors', $this -> AttributesCollectible -> validationErrors);
					}
				}
				//if everything is valid, then lets do our updates
				if($isValid) {
					//TODO move this to the model, validate we are removing the correct attributes?
					foreach($this -> data['AttributesCollectible'] as $key => $attribue) {
						//debug($this -> AttributesCollectible);
						if($attribue['action'] === 'D') {
							if($this -> AttributesCollectible -> delete($attribue['id'])) {

							}
						} else if($attribue['action'] === 'N') {
							$attribue['collectible_id'] = $id;
							$this -> AttributesCollectible -> set($attribue);
							if($this -> AttributesCollectible -> save()) {

							}
						}
					}
					$attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id), 'contain' => 'Attribute'));
					$this -> data = $attributes;
				} else {
					$errorAttributes = array();
					foreach($this->data['AttributesCollectible'] as $key => $attribue) {
						$attributesCollectible = array();
						$attributesCollectible['AttributesCollectible'] = $attribue;
						$attributesCollectible['Attribute'] = array();
						$attributesCollectible['Attribute']['name'] = $attribue['name'];
						array_push($errorAttributes, $attributesCollectible);
					}
					debug($errorAttributes);
					$this -> data = $errorAttributes;
				}
			}
		} else {
			//Submit the deletes as deletes....then loop throuh each one to either delete or add
			$attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id), 'contain' => 'Attribute'));
			debug($attributes);
			//$this -> set('attributes', $attributes);
			$this -> data = $attributes;
		}
	}

	function history($id=null) {
		$this -> checkLogIn();
		if($id && is_numeric($id)) {
			$attributeList = $this-> AttributesCollectible->find("list", array('conditions'=>array('AttributesCollectible.collectible_id'=>65)));
			debug($this-> AttributesCollectible->find("all", array('conditions'=> array('AttributesCollectible.id'=>array('17', '18','19')))));
			debug($attributeList);
			$this -> AttributesCollectible -> id = 37;
			$history = $this -> AttributesCollectible -> revisions(null, true);
			// $this -> loadModel('User');
			// //This is like the worst thing ever and needs to get cleaned up
			// //Making this by reference so we can modify it, is this proper in php?
			// foreach($history as $key => &$collectible) {
				// $userId = $collectible['Collectible']['user_id'];
				// $userDetails = $this -> User -> findById($userId, array('contain' => false));
				// $collectible['Collectible']['user_name'] = $userDetails['User']['username'];
			// }

			debug($history);
			$this -> set(compact('history'));

		} else {
			$this -> redirect($this -> referer());
		}
	}

}
?>