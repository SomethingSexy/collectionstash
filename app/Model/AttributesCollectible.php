<?php
class AttributesCollectible extends AppModel {
	public $name = 'AttributesCollectible';
	//var $useTable = 'accessories_collectibles';
	public $belongsTo = array('Attribute', 'Collectible', 'Revision');
	public $actsAs = array('Revision', 'Containable', 'Editable' => array('modelAssociations' => array('belongsTo' => array('Attribute')), 'type' => 'attribute', 'model' => 'AttributesCollectiblesEdit', 'compare' => array('count')));

	public $validate = array(

	//name field
	'count' => array('rule' => 'numeric', 'required' => true, 'message' => 'Must be numeric'),
	//manufacture field
	'attribute_id' => array('rule' => array('validateAttributeId'), 'required' => true, 'message' => 'Must be a valid item.'));

	function validateAttributeId($check) {
		debug($check['attribute_id']);
		if (!isset($check['attribute_id']) || empty($check['attribute_id'])) {
			return false;
		}
		$result = $this -> Attribute -> find('count', array('id' => $check['attribute_id']));

		return $result > 0;
	}

	public function beforeSave($options = array()) {
		if (isset($this -> data['Attribute'])) {

		}

		return true;
	}

	public function update($attributesCollectible, $userId, $autoUpdate = false) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		$this -> set($attributesCollectible);
		$validCollectible = true;
		// We can only update the count
		unset($this -> validate['attribute_id']);
		if ($this -> validates()) {
			// Now let's check to see if we need to update this based
			// on collectible status
			// If we are already auto updating, no need to check
			if ($autoUpdate === 'false' || $autoUpdate === false) {
				// find the AttributesCollectible and then use that to get the collectible id
				$currentVersion = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesCollectible.id' => $attributesCollectible['AttributesCollectible']['id'])));
				$isDraft = $this -> Collectible -> isStatusDraft($currentVersion['AttributesCollectible']['collectible_id']);
				debug($isDraft);
				if ($isDraft) {
					$autoUpdate = true;
				}
			}

			// If we are automatically approving it, then save it directly
			if ($autoUpdate === true || $autoUpdate === 'true') {
				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> EDIT, null);
				$attributesCollectible = array_merge($attributesCollectible, $revision);
				//$this -> id = $attribute['Attribute']['id'];
				if ($this -> saveAll($attributesCollectible, array('validate' => false))) {
					$updatedVersion = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesCollectible.id' => $this -> id)));
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data'] = $updatedVersion['AttributesCollectible'];
					$retVal['response']['data']['isEdit'] = false;
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 2;
				$attribute = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesCollectible.id' => $attributesCollectible['AttributesCollectible']['id'])));
				// TODO: We need to copy over the other details
				$attributesCollectible['AttributesCollectible']['collectible_id'] = $attribute['AttributesCollectible']['collectible_id'];
				$attributesCollectible['AttributesCollectible']['attribute_id'] = $attribute['AttributesCollectible']['attribute_id'];
				if ($this -> saveEdit($attributesCollectible, $attributesCollectible['AttributesCollectible']['id'], $userId, $action)) {
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data']['isEdit'] = true;
				}
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	/**
	 * This method will be used when we are trying to remove an attribute
	 */
	public function remove($attribute, $userId, $autoUpdate = false) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		// There will be an ['Attribute']['reason'] - input field
		// if this attribute is tied to a collectible, are we replacing
		// with an existing attriute? Or removing completely, which will
		// remove all references
		$action = array();
		$action['Action']['action_type_id'] = 4;
		$action['Action']['reason'] = $attribute['AttributesCollectible']['reason'];

		unset($attribute['AttributesCollectible']['reason']);
		$currentVersion = $this -> findById($attribute['AttributesCollectible']['id']);

		if ($autoUpdate === 'false' || $autoUpdate === false) {
			// find the AttributesCollectible and then use that to get the collectible id
			$currentVersion = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesCollectible.id' => $attribute['AttributesCollectible']['id'])));
			$isDraft = $this -> Collectible -> isStatusDraft($currentVersion['AttributesCollectible']['collectible_id']);
			debug($isDraft);
			if ($isDraft) {
				$autoUpdate = true;
			}
		}

		if ($autoUpdate === true || $autoUpdate === 'true') {

			$dataSource = $this -> getDataSource();
			$dataSource -> begin();

			if ($this -> delete($attribute['AttributesCollectible']['id'])) {
				$attributeCollectibles = $this -> find('all', array('conditions' => array('Attribute.id' => $currentVersion['Attribute']['id'])));

				// If there is only one, then delete the attribute as well
				if (count($attributeCollectibles['Attribute']) === 1) {
					// if this fails return false
					if ($this -> Attribute -> delete($attributeCollectibles['Attribute'][0]['id'])) {
						$retVal['response']['isSuccess'] = true;
						$dataSource -> commit();
					} else {
						$retVal['response']['isSuccess'] = false;
						$dataSource -> rollback();
					}
				} else {
					$dataSource -> commit();
					$retVal['response']['isSuccess'] = true;
				}

			} else {
				$retVal['response']['isSuccess'] = false;
				$dataSource -> rollback();
			}

			$retVal['response']['data']['isEdit'] = false;

		} else {
			// Doing this so that we have a record of the current version

			if ($this -> saveEdit($currentVersion, $attribute['AttributesCollectible']['id'], $userId, $action)) {
				$retVal['response']['isSuccess'] = true;
				$retVal['response']['data']['isEdit'] = true;
			} else {
				$retVal['response']['isSuccess'] = false;
			}
		}
		return $retVal;
	}

	/**
	 * This will add an attribute to a collectible.  It will need to
	 * handle a couple different scenarions.
	 *
	 * 1) Adding an existing attribute to a collectible
	 * 2) Adding a new attribute and linking that to a collectible
	 * 		This one will have to both add a new attribute and a new attribute collectible
	 *
	 */
	public function add($data, $userId, $autoUpdate = false) {
		// Check to see if there is an attribute id, if so then we are adding
		// from a previously selected attribute.  We can sumbit an edit for
		// this with the type of add

		// otherwise we are adding a brand new one
		// we need to submit an add for the attribute
		// Would that be a sperate edit or attached to this one?
		// Probably should be attached to this edit
		// and add for the attribute collectible

		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		$this -> set($data);
		$validCollectible = true;
		debug($data);
		// If we have an attribute, that means
		// we are saving a collectible attribute and creating a new
		// attribute at the same time, we don't have to validate
		// the attribute id because it does not exist yet
		if (isset($data['Attribute'])) {
			unset($this -> validate['attribute_id']);
		}

		if ($this -> validates()) {

			$dataSource = $this -> getDataSource();
			$dataSource -> begin();
			// If we hve an attribute we need to make sure
			// that that validates as well.
			if (isset($data['Attribute'])) {
				$this -> Attribute -> set($data);

				// If it doesn't validate return failz
				if (!$this -> Attribute -> validates()) {
					// Just in case
					$dataSource -> rollback();
					$retVal['response']['isSuccess'] = false;
					$errors = $this -> convertErrorsJSON($this -> Attribute -> validationErrors, 'Attribute');
					$retVal['response']['errors'] = $errors;
					return $retVal;
				}
				$attribute = array();
				$attribute['Attribute'] = $data['Attribute'];

				// Now we need to kick off a save of the attribute
				// This one doesn't matter if it is auto update or not because
				// these new ones will get approved based on approving of the collectible
				$attributeAddResponse = $this -> Attribute -> addAttribute($attribute, $userId);

				if ($attributeAddResponse && $attributeAddResponse['response']['isSuccess']) {
					debug($attributeAddResponse);
					$attributeId = $attributeAddResponse['response']['data']['Attribute']['id'];
					$data['AttributesCollectible']['attribute_id'] = $attributeId;
				} else {
					$dataSource -> rollback();
					// return that response, should be universal
					return $attributeAddResponse;
				}			}

			// Now let's check to see if we need to update this based
			// on collectible status
			// If we are already auto updating, no need to check
			if ($autoUpdate === 'false' || $autoUpdate === false) {
				$isDraft = $this -> Collectible -> isStatusDraft($data['AttributesCollectible']['collectible_id']);
				debug($isDraft);
				if ($isDraft) {
					$autoUpdate = true;
				}
			}

			if ($autoUpdate === true || $autoUpdate === 'true') {
				unset($data['Attribute']);
				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				if ($this -> saveAll($data, array('validate' => false))) {
					$dataSource -> commit();

					// Return what we just added
					$attributesCollectibleId = $this -> id;
					// Hopefully this won't be a performance issue at this level
					$attributesCollectible = $this -> find('first', array('conditions' => array('AttributesCollectible.id' => $attributesCollectibleId), 'contain' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'AttributesCollectible' => array('Collectible' => array('fields' => array('id', 'name')))))));

					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data'] = $attributesCollectible['AttributesCollectible'];
					$retVal['response']['data']['Attribute'] = $attributesCollectible['Attribute'];
					$retVal['response']['data']['Revision'] = $attributesCollectible['Revision'];
					$retVal['response']['data']['isEdit'] = false;
				} else {
					$dataSource -> rollback();
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 1;

				if ($this -> saveEdit($data, null, $userId, $action)) {
					// Only commit when the save edit is successful
					$dataSource -> commit();
					// I am not sure we ever need to return what we just
					// submitted if it is an edit
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data']['isEdit'] = true;
				} else {
					$dataSource -> rollback();
				}
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'AttributesCollectible');
			$retVal['response']['errors'] = $errors;
		}

		debug($retVal);

		return $retVal;
	}

	/**
	 * Ok should this method handle approving an attribute if it
	 */
	public function publishEdit($editId, $approvalUserId) {
		$retVal = false;
		// Grab the fields that will need to updated
		$attributeEditVersion = $this -> findEdit($editId);
		debug($attributeEditVersion);
		// Add
		if ($attributeEditVersion['Action']['action_type_id'] === '1') {
			// As of now all attributes are added to the main attribute table
			// when they are new, I cannot route it through the edit process
			// without some changes, this is easier for now
			if (isset($attributeEditVersion['Attribute'])) {
				if ($attributeEditVersion['Attribute']['status_id'] === '2') {
					$approval = array();
					$approval['Approval'] = array();
					$approval['Approval']['approve'] = 'true';
					$response = $this -> Attribute -> approve($attributeEditVersion['Attribute']['id'], $approval, $approvalUserId);
					// if this is false, then return false so that we won't be committing shit
					// otherwise carry on
					if (!$response['response']['isSuccess']) {
						return false;
					}
				}
			}

			$attributeCollectible = array();
			$attributeCollectible['AttributesCollectible']['count'] = $attributeEditVersion['AttributesCollectibleEdit']['count'];
			$attributeCollectible['AttributesCollectible']['attribute_id'] = $attributeEditVersion['AttributesCollectibleEdit']['attribute_id'];
			$attributeCollectible['AttributesCollectible']['collectible_id'] = $attributeEditVersion['AttributesCollectibleEdit']['collectible_id'];
			// Setting this as an add because it was added to the new table..not sure this is right
			$attributeCollectible['Revision']['action'] = 'A';
			$attributeCollectible['Revision']['user_id'] = $attributeEditVersion['AttributesCollectibleEdit']['edit_user_id'];
			if ($this -> saveAll($attributeCollectible, array('validate' => false))) {
				$retVal = true;
			}
		} else if ($attributeEditVersion['Action']['action_type_id'] === '4') {// Delete
			// If this attribute is only attached to one collectible, then delete the item too
			// At this point, you can't remove a pending one, so these have to be approved

			// TODO: We should check to see if there are any other edits for this one and remove them

			$attributeId = $attributeEditVersion['AttributesCollectibleEdit']['attribute_id'];

			$attributeCollectibles = $this -> find('all', array('conditions' => array('Attribute.id' => $attributeId)));

			if (!$this -> delete($attributeEditVersion['AttributesCollectibleEdit']['base_id'])) {
				return false;
			}

			// If there is only one, then delete the attribute as well
			if (count($attributeCollectibles) === 1) {
				// if this fails return false
				if (!$this -> Attribute -> delete($attributeId)) {
					return false;
				}
			}

			$retVal = true;

		} else if ($attributeEditVersion['Action']['action_type_id'] === '2') {// Edit

			$attributeCollectible = array();
			$attributeCollectible['AttributesCollectible']['count'] = $attributeEditVersion['AttributesCollectibleEdit']['count'];
			$attributeCollectible['AttributesCollectible']['id'] = $attributeEditVersion['AttributesCollectibleEdit']['base_id'];
			$attributeCollectible['Revision']['action'] = 'A';
			$attributeCollectible['Revision']['user_id'] = $attributeEditVersion['AttributesCollectibleEdit']['edit_user_id'];
			if ($this -> saveAll($attributeCollectible, array('validate' => false))) {
				$retVal = true;
			}
		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $attributeEditVersion['AttributesCollectibleEdit']['collectible_id'])));
			$message = 'We have approved the following collectible part you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $attributeEditVersion['AttributesCollectibleEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($attributeEditVersion['AttributesCollectibleEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	/**
	 * This method will deny the edit, in which case we will be deleting it
	 */
	public function denyEdit($editId) {
		$retVal = false;
		debug($editId);
		// Grab the fields that will need to updated
		$attributesCollectibleEdit = $this -> findEdit($editId);
		debug($attributesCollectibleEdit);
		// Right now we can really only add or edit
		if ($attributesCollectibleEdit['Action']['action_type_id'] === '1') {//Add
			// If we are adding, we need to check and see if the attribute is new or
			// existing.
			// If it is new, then we will also be deleting that
			if ($this -> deleteEdit($attributesCollectibleEdit)) {
				if ($attributesCollectibleEdit['Attribute']['status_id'] === '2') {
					if ($this -> Attribute -> delete($attributesCollectibleEdit['Attribute']['id'])) {
						$retVal = true;
					}
				} else {
					$retVal = true;
				}
			}
		} else if ($attributesCollectibleEdit['Action']['action_type_id'] === '2') {// Edit
			if ($this -> deleteEdit($attributesCollectibleEdit)) {
				$retVal = true;
			}

		} else if ($attributesCollectibleEdit['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($attributesCollectibleEdit)) {
				$retVal = true;
			}

		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $attributesCollectibleEdit['AttributesCollectibleEdit']['collectible_id'])));
			$message = 'We have denied the following collectible part you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $attributesCollectibleEdit['AttributesCollectibleEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($attributesCollectibleEdit['AttributesCollectibleEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	/**
	 * This method will return any AttributeCollectibles that might have edits
	 *  for a given attribute.  Right now this is mainly being used to determine
	 *  if a new attribute is already tied to a collectible attribute
	 */
	public function findEditsByAttributeId($attributeId) {
		return $this -> EditModel -> find("all", array('contain' => false, 'conditions' => array($this -> EditModel -> alias . '.attribute_id' => $attributeId)));
	}

	public function validateAttrbitue($attribute) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		$this -> set($attribute);
		$validCollectible = true;
		if ($this -> validates()) {
			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'AttributesCollectible');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

}
?>
