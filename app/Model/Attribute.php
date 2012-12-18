<?php
/**
 * I should probably make the uploads table indepdent of an attribute and a collectible and then have join tables for each.
 *
 * Need to add an action here so we know if we are deleting or editing or adding
 *
 * I feel like we should be pulling this from a revision but until it is committed it doesn't get a revision, so we need the action]
 * on the edit
 *
 * This will tell us from the UI, what we are doing with this attribute, because we might be removing it and if we are then
 * we need to see if we are linking the current collectibles to another
 *
 *
 */
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class Attribute extends AppModel {
	public $name = 'Attribute';
	public $hasMany = array('AttributesCollectible' => array('dependent' => true));
	public $belongsTo = array('Status', 'Manufacture', 'Scale', 'AttributeCategory', 'Revision' => array('dependent' => true), 'User', 'EntityType' => array('dependent' => true));
	public $actsAs = array('Revision' => array('model' => 'AttributeRev'), 'Containable', 'Editable' => array('type' => 'attribute', 'model' => 'AttributeEdit', 'modelAssociations' => array('belongsTo' => array('Scale', 'Manufacture', 'AttributeCategory')), 'compare' => array('name', 'description', 'scale_id', 'manufacture_id', 'attribute_category_id')));
	public $findMethods = array('standalone' => true, 'collectible' => true);

	public $validate = array(

	//name field
	//'name' => array('rule' => "/^[A-Za-z0-9\s#:.-]+\z/", 'required' => true, 'message' => 'Invalid characters'),
	//Opening this up because I don't see it being a big deal.
	'name' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Name is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')),
	//manufacture field
	'manufacture_id' => array('rule' => array('validateManufactureId'), 'required' => true, 'message' => 'Must be a valid manufacture.'),
	//series field
	'attribute_category_id' => array('rule' => array('validateCategoryId'), 'required' => true, 'message' => 'Please select a valid category.'),
	//description field
	'description' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Description is required.'), 'maxLength' => array('rule' => array('maxLength', 1000), 'message' => 'Invalid length.')));

	function beforeSave() {
		//Make sure there is no space around the email, seems to be an issue with sending when there is
		return true;
	}

	function validateManufactureId($check) {
		if (isset($check['manufacture_id']) && !empty($check['manufacture_id']) && is_numeric($check['manufacture_id'])) {
			$result = $this -> Manufacture -> find('count', array('id' => $check['manufacture_id']));
			return $result > 0;
		}
		return false;
	}

	/*
	 * This is going to validate the series based on the manufacturer.  If the manufacturer does not
	 * have a series id set, then it will let it pass as null
	 *
	 * If the manufacturer does have a series id, then a series id MUST be set.
	 */
	function validateCategoryId($check) {
		//Check to see if a series is set
		if (isset($check['attribute_category_id']) && !empty($check['attribute_category_id']) && is_numeric($check['attribute_category_id'])) {
			$result = $this -> AttributeCategory -> find('count', array('id' => $check['attribute_category_id']));
			return $result > 0;
		}
		return false;

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
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	/**
	 * This method will be used when we are updating an attribute
	 */
	public function update($attribute, $userId, $autoUpdate = false) {
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
			// If we are automatically approving it, then save it directly
			if ($autoUpdate) {
				unset($attribute['AttributesCollectible']);
				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> EDIT, null);
				$attribute = array_merge($attribute, $revision);
				//$this -> id = $attribute['Attribute']['id'];
				if ($this -> saveAll($attribute, array('validate' => false))) {
					$retVal['response']['isSuccess'] = true;
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 2;

				if ($this -> saveEdit($attribute, $attribute['Attribute']['id'], $userId, $action)) {
					$retVal['response']['isSuccess'] = true;
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
		$action['Action']['reason'] = $attribute['Attribute']['reason'];

		unset($attribute['Attribute']['reason']);

		// Doing this so that we have a record of the current version
		$currentVersion = $this -> findById($attribute['Attribute']['id']);
		$currentVersion['Attribute']['link'] = $attribute['Attribute']['link'];

		if (!$attribute['Attribute']['link']) {
			unset($attribute['Attribute']['replace_attribute_id']);
		} else {
			$currentVersion['Attribute']['replace_attribute_id'] = $attribute['Attribute']['replace_attribute_id'];
		}

		if ($this -> saveEdit($currentVersion, $attribute['Attribute']['id'], $userId, $action)) {
			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
		}
		debug($retVal);
		return $retVal;
	}

	public function addAttribute($attribute, $userId) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();

		// An id of 2 means it is submitted
		$attribute['Attribute']['status_id'] = 2;
		$attribute['Attribute']['user_id'] = $userId;
		$attribute['EntityType']['type'] = 'attribute';
		$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
		$attribute = array_merge($attribute, $revision);
		if ($this -> saveAssociated($attribute)) {
			$attributeId = $this -> id;
			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = array();
			$retVal['response']['data']['Attribute'] = array();
			$retVal['response']['data']['Attribute']['id'] = $attributeId;
			$retVal['response']['isSuccess'] = true;
			$addAttribute = $this -> find('first', array('conditions' => array('Attribute.id' => $attributeId)));
			$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_SUBMIT_NEW, 'user' => $addAttribute, 'object' => $addAttribute, 'type' => 'Attribute')));
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	/**
	 * This method is used when we are approving new attributes
	 *
	 * Return Codes:
	 * 		1: Successly Approved
	 * 		2: Successly Denied
	 * 		4: Error saving
	 * 		5: Attribute has been approved already
	 */
	public function approve($id, $approval, $userId) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;

		$attribute = $this -> find('first', array('conditions' => array('Attribute.id' => $id), 'contain' => array('User', 'Manufacture', 'Scale', 'Status')));

		if ($approval['Approval']['approve'] === 'true') {
			// 2 is the approvel status now
			if (!empty($attribute) && $attribute['Attribute']['status_id'] === '2') {
				$data = array();
				$data['Attribute'] = array();
				$data['Attribute']['id'] = $attribute['Attribute']['id'];
				$data['Attribute']['status_id'] = 4;

				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> APPROVED, $approval['Approval']['notes']);
				$data = array_merge($data, $revision);

				if ($this -> saveAll($data, array('validate' => false))) {
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['code'] = 1;
					if ($retVal) {
						$approver = $this -> User -> find('first', array('conditions'=> array('User.id'=> $userId)));
						$submitter = $this -> User -> find('first', array('conditions'=> array('User.id'=> $attribute['Attribute']['user_id'])));
						$message = 'We have approved the following collectible part you added <a href="http://' . env('SERVER_NAME') . '/attributes/view/' . $attribute['Attribute']['id'] . '">' . $attribute['Attribute']['name'] . '</a>';
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADMIN_APPROVE_NEW, 'user' => $approver, 'object' => $attribute, 'target' => $submitter, 'type' => 'Attribute')));
						$this -> notifyUser($attribute['Attribute']['user_id'], $message);
					}
				} else {
					$retVal['response']['code'] = 4;
				}
			} else {
				$retVal['response']['code'] = 5;
			}
		} else {
			//fuck it, I am deleting it
			if ($this -> delete($attribute['Attribute']['id'], true)) {
				$retVal['response']['code'] = 2;
				if ($retVal) {
					$message = 'We have denied the following collectible part you added <a href="http://' . env('SERVER_NAME') . '/attributes/view/' . $attribute['Attribute']['id'] . '">' . $attribute['Attribute']['name'] . '</a>';
					$this -> notifyUser($attribute['Attribute']['user_id'], $message);
				}
			} else {
				$retVal['response']['code'] = 4;
			}

		}

		return $retVal;
	}

	public function denyEdit($editId) {
		$retVal = false;
		debug($editId);
		// Grab the fields that will need to updated
		$attributeEditVersion = $this -> findEdit($editId);
		debug($attributeEditVersion);
		// Right now we can really only add or edit
		if ($attributeEditVersion['Action']['action_type_id'] === '1') {//Add
			//TODO: Add does not go through here yet so it should not happen

		} else if ($attributeEditVersion['Action']['action_type_id'] === '2') {// Edit
			if ($this -> deleteEdit($attributeEditVersion)) {
				$retVal = true;
			}

		} else if ($attributeEditVersion['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($attributeEditVersion)) {
				$retVal = true;
			}

		}

		if ($retVal) {
			$name = $attributeEditVersion['AttributeEdit']['description'];
			if (!empty($attributeEditVersion['AttributeEdit']['name'])) {
				$name = $attributeEditVersion['AttributeEdit']['name'];
			}

			$message = 'We have denied the following collectible part you submitted a change to <a href="http://' . env('SERVER_NAME') . '/attributes/view/' . $attributeEditVersion['AttributeEdit']['base_id'] . '">' . $name . '</a>';
			$this -> notifyUser($attributeEditVersion['AttributeEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	public function publishEdit($editId) {
		$retVal = false;
		// Grab the fields that will need to updated
		$attributeEditVersion = $this -> findEdit($editId);
		debug($attributeEditVersion);
		$attributeFields = array();
		if ($attributeEditVersion['Action']['action_type_id'] === '1') {
			// Don't need to do much with add because it doesn't come down this way yet
			// I am not sure this will ever handle NEW attributes.  The reason being
			// If I am adding a new attribute to a collectible, I won't have that direct
			// link without a lot of hacking
			$attributeFields['Revision']['action'] = 'A';
		} else if ($attributeEditVersion['Action']['action_type_id'] === '4') {
			$proceed = true;
			debug($attributeEditVersion['AttributeEdit']['replace_attribute_id']);
			// If we have a replacement, then lets update all of those first
			if (isset($attributeEditVersion['AttributeEdit']['replace_attribute_id']) && !empty($attributeEditVersion['AttributeEdit']['replace_attribute_id'])) {
				$replacementAttribute = $attributeEditVersion['AttributeEdit']['replace_attribute_id'];

				// Need to grab this before hand
				$updateAttributesCollectible = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.attribute_id' => $attributeEditVersion['AttributeEdit']['base_id'])));
				// Need to manually specify the modified field because updateAll is very dumb, only does what you tell it to do
				if ($this -> AttributesCollectible -> updateAll(array('AttributesCollectible.attribute_id' => $replacementAttribute, 'AttributesCollectible.modified' => 'NOW()'), array('AttributesCollectible.attribute_id' => $attributeEditVersion['AttributeEdit']['base_id']))) {

					// Seems like a lot of redundancy
					// Since updateAll does not trigger afterSave we need to manually create revisions
					foreach ($updateAttributesCollectible as $key => $value) {
						debug($value['AttributesCollectible']['id']);
						// TODO: This is not working
						$this -> AttributesCollectible -> id = $value['AttributesCollectible']['id'];
						$this -> AttributesCollectible -> createRevision();
					}

					$retVal = true;
				}
			}

			// If the update of the attributes failed then don't try the delete
			// If I am not replacing and it is linked, this delete will automatically delete
			// all dependent AttributesCollectible rows
			if ($proceed) {
				if ($this -> delete($attributeEditVersion['AttributeEdit']['base_id'])) {
					$retVal = true;
				}
			}
		} else if ($attributeEditVersion['Action']['action_type_id'] === '2') {
			$attributeFields['Attribute']['attribute_category_id'] = $attributeEditVersion['AttributeEdit']['attribute_category_id'];
			$attributeFields['Attribute']['name'] = $attributeEditVersion['AttributeEdit']['name'];
			$attributeFields['Attribute']['description'] = $attributeEditVersion['AttributeEdit']['description'];
			$attributeFields['Attribute']['manufacture_id'] = $attributeEditVersion['AttributeEdit']['manufacture_id'];
			$attributeFields['Attribute']['scale_id'] = $attributeEditVersion['AttributeEdit']['scale_id'];
			$attributeFields['Attribute']['id'] = $attributeEditVersion['AttributeEdit']['base_id'];
			$attributeFields['Revision']['action'] = 'E';
			$attributeFields['Revision']['user_id'] = $attributeEditVersion['AttributeEdit']['edit_user_id'];

			// commit it

			// Return a true or false for this method if it is successful or not
			//
			if ($this -> saveAll($attributeFields, array('validate' => false))) {
				$retVal = true;
			}
		}

		if ($retVal) {
			$name = $attributeEditVersion['AttributeEdit']['description'];
			if (!empty($attributeEditVersion['AttributeEdit']['name'])) {
				$name = $attributeEditVersion['AttributeEdit']['name'];
			}

			$message = 'We have approved the following collectible part you submitted a change to <a href="http://' . env('SERVER_NAME') . '/attributes/view/' . $attributeEditVersion['AttributeEdit']['base_id'] . '">' . $name . '</a>';
			$this -> notifyUser($attributeEditVersion['AttributeEdit']['edit_user_id'], $message);
		}

		return $retVal;

	}

	/**
	 * Custom find method to find all of the attributes that are standlone, i.e., not tied
	 * to a collectible yet
	 */
	protected function _findStandalone($state, $query, $results = array()) {
		if ($state == 'before') {
			return $query;
		} else if ($state == 'after') {
			foreach ($results as $key => $value) {
				// See if there are any edits that exist for this collectible, if there are that means this was
				// add as a part of an edit to adding one directly to  a collectible...don't show
				$edits = $this -> AttributesCollectible -> findEditsByAttributeId($value['Attribute']['id']);
				if (isset($edits) && !empty($edits)) {
					unset($results[$key]);
				} else {
					// We can also from the admin, automatically add Attributes Collectible and Attribute
					//
					if (!empty($value['AttributesCollectible'])) {
						unset($results[$key]);
					}
				}

			}

			return $results;
		}
	}

	/**
	 * custom find method to fina all of the attributes that are tied to a collectible
	 * already
	 */
	protected function _findCollectible($state, $query, $results = array()) {
		if ($state == 'before') {
			return $query;
		} else if ($state == 'after') {
			foreach ($results as $key => $value) {
				$edits = $this -> AttributesCollectible -> findEditsByAttributeId($value['Attribute']['id']);
				// If this doesn't have edits, check to see if it does have a collectible attached, since it is new
				// and it does have one attached it might have been an admin add
				if (!empty($edits)) {
					// do nothing
				} else {
					// If it doesn't have any edits see if
					if (!empty($value['AttributesCollectible'])) {
						// do nothing
					} else {
						unset($results[$key]);
					}
				}
			}

			return $results;
		}
	}

}
?>