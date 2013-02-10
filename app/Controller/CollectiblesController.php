<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesController extends AppController {

	public $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'CollectibleDetail', 'Minify', 'Tree');

	// public $components = array('Wizard');

	var $actsAs = array('Searchable.Searchable');

	// function beforeFilter() {
	// parent::beforeFilter();
	// // $this -> Wizard -> steps = array('manufacture', array('variant' => 'variantFeatures'), 'attributes', 'tags', 'image', 'review');
	// $this -> Wizard -> steps = array('manufacture', array('similar' => 'similarCollectibles'), 'attributes', 'tags', 'image', 'review');
	// $this -> Wizard -> completeUrl = '/collectibles/confirm';
	// $this -> Wizard -> loginRequired = true;
	// }

	/**
	 * This method will allow us to quick add a collectible from a selected collectible.
	 * This method will base the new collectible off the manufacture and type that this collectible is.
	 *
	 * We will probably want to check against the type of collectible first in the future to know what
	 * we are doing first.
	 *
	 * Use Cases:
	 * 	- Add a similar collectible (collectible Id = xx, variant = false)
	 *  - Add a variant collectible (collectible Id = xx, variant = true)
	 *  - Add a similar collectible that is a variant of a base collectible (collectible Id = xx, variant = false)
	 * 		- For this case, we will determine here IF the collectible we are copying from IS a variant, then we will use its base collectible as the base collectible for the new collectible
	 *
	 * $collectibleId - this is the id that we are "Copying"
	 * $variant - this is a variant add?
	 */
	function quickCreate($collectibleId = null, $variant = false) {
		$this -> checkLogIn();
		if (!is_null($collectibleId) && is_numeric($collectibleId)) {

			if ($variant === 'true') {
				//If we are adding a variant, copy the collectible completely
				// setting its parent to this collectible and making it a variant
				$response = $this -> Collectible -> createCopy($collectibleId, $this -> getUserId(), true);
				debug($response);
				if ($response['response']['isSuccess']) {
					$this -> redirect(array('action' => 'edit', $response['response']['data']['collectible_id']));
				} else {

				}
			} else {

			}

		} else {
			$this -> redirect($this -> referer());
		}
	}

	/**
	 * This will be used to create a new collectible, with just
	 * the type to start
	 */
	public function create($collectibleTypeId = null) {
		$this -> checkLogIn();
		if ($collectibleTypeId && is_numeric($collectibleTypeId)) {
			$response = $this -> Collectible -> createInitial($collectibleTypeId, $this -> getUserId());
			debug($response);
			if ($response['response']['isSuccess']) {
				$this -> redirect(array('action' => 'edit', $response['response']['data']['id']));
			} else {
				debug($response);
			}
		}

		// Always do this in case there is an error
		$collectibleTypes = $this -> Collectible -> Collectibletype -> find('threaded', array('contain' => false));
		$this -> set(compact('collectibleTypes'));
	}

	public function admin_edit($id) {
		// Need to check login
		$this -> checkLogIn();
		$this -> checkAdmin();
		// Need to get the collectible
		// Based on status and the logged in user, need to determine if we can proceed

		// If the status is draft, then the only person who can edit it is the person who submitted it
		// If the status is submitted, then the only person who can edit it is the persno who submitted it and an admin
		// If the status is active, then anyone can edit it
		$collectible = $this -> Collectible -> find('first', array('contain' => array('Status'), 'conditions' => array('Collectible.id' => $id)));

		if (!empty($collectible)) {
			$statusId = $collectible['Status']['id'];
			$submittedUserId = $collectible['Collectible']['user_id'];
			if ($statusId === '1') {
				if ($submittedUserId !== $this -> getUserId()) {
					$this -> render('editAccess');
					return;
				}
			} else if ($statusId === '2') {
				if ($submittedUserId !== $this -> getUserId() && !$this -> isUserAdmin()) {
					$this -> render('editAccess');
					return;
				}

			} else if ($statusId === '4') {
				// always access
			}
		} else {
			$this -> render('viewMissing');
			return;
		}

		// This is the basic stuff to get for edit attributes
		$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
		$this -> set(compact('attributeCategories'));

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
		$this -> set(compact('manufactures'));

		// Pass the id to the view to use
		$this -> set('collectibleId', $id);
		$this -> set('adminMode', true);
		$this -> render('edit');
	}

	/**
	 * New view but not sure what this is going to do yet
	 */
	public function edit($id) {
		// Need to check login
		$this -> checkLogIn();
		// Need to get the collectible
		// Based on status and the logged in user, need to determine if we can proceed

		// If the status is draft, then the only person who can edit it is the person who submitted it
		// If the status is submitted, then the only person who can edit it is the persno who submitted it and an admin
		// If the status is active, then anyone can edit it
		$collectible = $this -> Collectible -> find('first', array('contain' => array('Status'), 'conditions' => array('Collectible.id' => $id)));

		if (!empty($collectible)) {
			$statusId = $collectible['Status']['id'];
			$submittedUserId = $collectible['Collectible']['user_id'];
			if ($statusId === '1') {
				if ($submittedUserId !== $this -> getUserId()) {
					$this -> render('editAccess');
					return;
				}
			} else if ($statusId === '2') {
				if ($submittedUserId !== $this -> getUserId() && !$this -> isUserAdmin()) {
					$this -> render('editAccess');
					return;
				}

			} else if ($statusId === '4') {
				// always access
			}
		} else {
			$this -> render('viewMissing');
			return;
		}

		// This is the basic stuff to get for edit attributes
		$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
		$this -> set(compact('attributeCategories'));

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
		$this -> set(compact('manufactures'));

		// Pass the id to the view to use
		$this -> set('collectibleId', $id);
	}

	public function collectible($adminMode = false, $id = null) {
		// check login
		// check to make sure they can make this change
		if ($adminMode === true) {
			if (!$this -> isUserAdmin()) {
				$this -> response -> statusCode(401);
				return;
			}
		}

		if ($this -> request -> isPut()) {
			$collectible['Collectible'] = $this -> request -> input('json_decode', true);
			$collectible['Collectible'] = Sanitize::clean($collectible['Collectible']);

			$this -> Collectible -> saveCollectible($collectible, $this -> getUser(), $adminMode);
			$this -> set('returnData', $this -> request -> input('json_decode'));
		} else if ($this -> request -> isDelete()) {
			// I think it makes sense to use rest delete
			// for changing the status to a delete
			// although I am going to physically delete it
			// not change the status :)
			$response = $this -> Collectible -> remove($id, $this -> getUser());

			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);

		}

	}

	public function status($id) {
		// check login
		// check to make sure they can make this change

		if ($this -> request -> isPut()) {
			$data = $this -> request -> input('json_decode', true);
			$hasDupList = false;
			if (isset($data['hasDupList']) && $data['hasDupList']) {
				$hasDupList = true;
			}
			$response = $this -> Collectible -> updateStatus($id, $this -> getUser(), $hasDupList);

			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
			// we need to check the response here
		}

	}

	// This will handle the updating of tags
	public function tag($adminMode = false, $id = null) {
		// WE are handling one tag at a time here
		// if it is a put, then we are adding
		// if it is a delete then we are removing
		debug($adminMode);

		if ($adminMode === true || $adminMode === 'true') {
			if (!$this -> isUserAdmin()) {
				$this -> response -> statusCode(401);
				return;
			}
		}

		if ($this -> request -> isPost()) {
			$collectible['CollectiblesTag'] = $this -> request -> input('json_decode', true);
			$response = $this -> Collectible -> CollectiblesTag -> add($collectible, $this -> getUser(), $adminMode);
			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
			// we need to check the response here
		} else if ($this -> request -> isDelete()) {
			$collectible['CollectiblesTag'] = array();
			$collectible['CollectiblesTag']['id'] = $id;
			$response = $this -> Collectible -> CollectiblesTag -> remove($collectible, $this -> getUser(), $adminMode);
			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
		}
	}

	// This will handle the updating of artists
	public function artist($adminMode = false, $id = null) {
		// WE are handling one tag at a time here
		// if it is a put, then we are adding
		// if it is a delete then we are removing
		debug($adminMode);

		if ($adminMode === true || $adminMode === 'true') {
			if (!$this -> isUserAdmin()) {
				$this -> response -> statusCode(401);
				return;
			}
		}

		if ($this -> request -> isPost()) {
			$collectible['ArtistsCollectible'] = $this -> request -> input('json_decode', true);
			$response = $this -> Collectible -> ArtistsCollectible -> add($collectible, $this -> getUser(), $adminMode);
			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
			// we need to check the response here
		} else if ($this -> request -> isDelete()) {
			$collectible['ArtistsCollectible'] = array();
			$collectible['ArtistsCollectible']['id'] = $id;
			$response = $this -> Collectible -> ArtistsCollectible -> remove($collectible, $this -> getUser(), $adminMode);
			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
		}
	}

	/**
	 * this method will be used to allow them to delete a collectible
	 */
	public function delete() {

	}

	public function getCollectible($id) {
		$returnData = $this -> Collectible -> getCollectible($id);
		$collectibleTypeId = $returnData['response']['data']['collectible']['Collectible']['collectibletype_id'];
		// We will also want to get the manufacturers and their licenses right away
		$manufacturerCollectibletypes = $this -> Collectible -> Manufacture -> CollectibletypesManufacture -> find('all', array('conditions' => array('CollectibletypesManufacture.collectibletype_id' => $collectibleTypeId), 'contain' => array('Manufacture' => array('LicensesManufacture' => array('License')))));

		// also based on the type of collectible we might want to get all brands
		// right now this is for prints that might not have a manufacturer and
		// just in case we want to link it to a brand
		if ($collectibleTypeId === Configure::read('Settings.CollectibleTypes.Print')) {
			$brands = $this -> Collectible -> License -> find('all', array('contain' => false));
			$returnData['response']['data']['brands'] = $brands;
		} else {
			$returnData['response']['data']['brands'] = array();
		}

		$manList = array();
		foreach ($manufacturerCollectibletypes as $key => $value) {
			array_push($manList, $value['Manufacture']);
		}
		$returnData['response']['data']['manufacturers'] = $manList;

		//Grab all scales
		$scales = $this -> Collectible -> Scale -> find("all", array('contain' => false, 'fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$returnData['response']['data']['scales'] = $scales;

		//Grab all retailers.
		$retailers = $this -> Collectible -> Retailer -> find('all', array('contain' => false));
		$returnData['response']['data']['retailers'] = $retailers;

		//Grab all currencies
		$currencies = $this -> Collectible -> Currency -> find("all", array('contain' => false, 'fields' => array('Currency.id', 'Currency.iso_code')));
		$returnData['response']['data']['currencies'] = $currencies;

		$this -> set(compact('returnData'));
	}

	function view($id = null) {
		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		// TODO: We really need to start caching collectibles I think...we are fetching A LOT of data
		// 12/17/12 - Welp I was right, this is WAY too many joins for my little server to handle
		//$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'AttributesCollectible' => array('Collectible' => array('fields' => array('id', 'name')))), 'conditions' => array('AttributesCollectible.active' => 1)))));
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('ArtistsCollectible' => array('Artist'), 'Status', 'Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale'), 'conditions' => array('AttributesCollectible.active' => 1)))));

		// so let's do this manually and try that out
		if (!empty($collectible['AttributesCollectible'])) {
			// ok if we have some of these
			// loop through each one
			foreach ($collectible['AttributesCollectible'] as $key => $attributesCollectible) {
				//'AttributesCollectible' => array('Collectible' )
				if (!empty($attributesCollectible['Attribute'])) {
					$existingAttributeCollectibles = $this -> Collectible -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.attribute_id' => $attributesCollectible['Attribute']['id']), 'contain' => array('Collectible' => array('fields' => array('id', 'name')))));
					$collectible['AttributesCollectible'][$key]['Attribute']['AttributesCollectible'] = $existingAttributeCollectibles;
				}
			}
		}

		// View should also work for status of submitted and active
		if (!empty($collectible) && ($collectible['Collectible']['status_id'] === '4' || $collectible['Collectible']['status_id'] === '2')) {
			if ($collectible['Collectible']['status_id'] === '2') {
				$this -> set('showStatus', true);
				if ($collectible['Collectible']['user_id'] === $this -> getUserId()) {
					$this -> set('allowStatusEdit', true);
				} else {
					$this -> set('allowStatusEdit', false);
				}

			} else {
				$this -> set('showStatus', false);
				$this -> set('allowStatusEdit', false);
			}

			$this -> set('collectible', $collectible);
			$count = $this -> Collectible -> getNumberofCollectiblesInStash($id);
			$this -> set('collectibleCount', $count);

			$variants = $this -> Collectible -> getCollectibleVariants($id);
			$this -> set('variants', $variants);

			//TODO: this should be a helper or something to get all of the data necessary to render the add attribute window
			$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
			$this -> set(compact('attributeCategories'));

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
			$this -> set(compact('scales'));

			$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
			$this -> set(compact('manufactures'));

		} else {
			$this -> render('viewMissing');
		}
	}

	function search() {
		/*
		 * Call the parent method now, that method handles pretty much everything now
		 */
		$this -> searchCollectible();
		// I can use this to pull the pagination data off the request and pass it to the view
		// although in the JSON view, I should be able to pull all of the data off the request
		// and build out the JSON object and send that down, with access to the pagination
		// information.  I can pass it as meta data that the client side script can then use
		// to know how to make the next set of requests
		if ($this -> request -> isAjax()) {
			$this -> render('searchJson');
		} else {
			$this -> set('viewType', 'list');
			$this -> render('searchList');
		}

	}

	/**
	 * We need to two methods because the tile stuff using the infinite scroll
	 * which uses the standard HTML response to parse out the contents
	 */
	function searchTiles($type = 'list') {
		/*
		 * Call the parent method now, that method handles pretty much everything now
		 */
		$this -> searchCollectible();
		$this -> set('viewType', 'tiles');
		$this -> render('searchTiles');

	}

	function history($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			$this -> Collectible -> id = $id;
			$history = $this -> Collectible -> revisions(null, true);
			$this -> loadModel('User');

			//TODO the revision behavior needs to get updated so that we can return associated data with it
			//Maybe the revision behavior should also interact with the Revision model
			//Making this by reference so we can modify it, is this proper in php?
			foreach ($history as $key => &$collectible) {
				$collectibleRevision = $this -> Collectible -> Revision -> findById($collectible['Collectible']['revision_id'], array('contain' => false));
				$collectible['Collectible']['action'] = $collectibleRevision['Revision']['action'];
				if ($collectibleRevision['Revision']['action'] !== 'A') {
					$editUserDetails = $this -> User -> findById($collectibleRevision['Revision']['user_id'], array('contain' => false));
					$collectible['Collectible']['user_name'] = $editUserDetails['User']['username'];
				} else {
					$userId = $collectible['Collectible']['user_id'];
					$userDetails = $this -> User -> findById($userId, array('contain' => false));
					$collectible['Collectible']['user_name'] = $userDetails['User']['username'];
				}
			}

			$this -> set(compact('history'));
			debug($history);
			//Grab a list of all attributes associated with this collectible, or were associated with this collectible.  We will display a list of all
			//of these attributes then we can go into further history detail if we need too
			$attributeHistory = $this -> Collectible -> AttributesCollectible -> find("all", array('conditions' => array('AttributesCollectible.collectible_id' => $id)));
			$this -> set(compact('attributeHistory'));
			//Update this in the future since we only allow one Upload for now
			$collectibleUpload = $this -> Collectible -> Upload -> find("first", array('contain' => false, 'conditions' => array('Upload.collectible_id' => $id)));
			$uploadHistory = array();
			if (!empty($collectibleUpload)) {
				$this -> Collectible -> Upload -> id = $collectibleUpload['Upload']['id'];
				$uploadHistory = $this -> Collectible -> Upload -> revisions(null, true);
				//This is like the worst thing ever and needs to get cleaned up
				//Making this by reference so we can modify it, is this proper in php?
				foreach ($uploadHistory as $key => &$upload) {
					$uploadRevision = $this -> Collectible -> Revision -> findById($upload['Upload']['revision_id'], array('contain' => false));

					$upload['Upload']['action'] = $uploadRevision['Revision']['action'];

					$editUserDetails = $this -> User -> findById($uploadRevision['Revision']['user_id'], array('contain' => false));
					$upload['Upload']['user_name'] = $editUserDetails['User']['username'];

				}
				//As of 9/7/11, because of the way we have to add an upload, the first revision is going to be bogus.
				//Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
				$lastUpload = end($uploadHistory);
				if ($lastUpload['Upload']['revision_id'] === '0') {
					array_pop($uploadHistory);
				}
				reset($uploadHistory);

			}

			$this -> set(compact('uploadHistory'));
		} else {
			$this -> redirect($this -> referer());
		}
	}

	function historyDetail($id = null, $version_id = null) {
		$this -> checkLogIn();

		if ($id && $version_id && is_numeric($id) && is_numeric($version_id)) {
			$this -> Collectible -> id = $id;
			$collectible = $this -> Collectible -> revisions(array('conditions' => array('version_id' => $version_id)), true);

			$this -> set(compact('collectible'));

		} else {
			//$this -> redirect($this -> referer());
		}
	}

	/**
	 * This function right now will return the history of the collectibles the user has submitted.
	 */
	function userHistory() {
		//Make sure the user is logged in
		$this -> checkLogIn();
		//Grab the user id of the person logged in
		$userId = $this -> getUserId();
		$this -> paginate = array('conditions' => array('Collectible.user_id' => $userId), 'contain' => array('Collectibletype', 'Manufacture', 'Status'), 'limit' => 10);
		$collectibles = $this -> paginate('Collectible');
		$this -> set(compact('collectibles'));
	}

	function newCollectibles() {
		//Make sure the user is logged in
		$this -> checkLogIn();
		$this -> paginate = array('conditions' => array('Collectible.status_id' => 4), 'order' => array('Collectible.modified' => 'desc'), 'contain' => array('Collectibletype', 'Manufacture', 'Status', 'CollectiblesUpload' => array('Upload')), 'limit' => 5);
		$collectibles = $this -> paginate('Collectible');
		$this -> set(compact('collectibles'));
	}

	function pending() {
		//Make sure the user is logged in
		$this -> checkLogIn();
		$this -> paginate = array('conditions' => array('Collectible.status_id' => 2), 'contain' => array('Collectibletype', 'Manufacture', 'Status', 'CollectiblesUpload' => array('Upload')), 'limit' => 5);
		$collectibles = $this -> paginate('Collectible');
		$this -> set(compact('collectibles'));
	}

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$this -> paginate = array("conditions" => array('status_id' => 2), "contain" => array('Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')));

		$collectilbes = $this -> paginate('Collectible');

		$this -> set('collectibles', $collectilbes);

	}

	function admin_view($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('ArtistsCollectible' => array('Artist'), 'CollectiblesTag' => array('Tag'), 'Status', 'Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'Revision'), 'conditions' => array('AttributesCollectible.active' => 1)))));
		$this -> set('collectible', $collectible);
		debug($collectible);

		//TODO: this should be a helper or something to get all of the data necessary to render the add attribute window
		$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
		$this -> set(compact('attributeCategories'));

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
		$this -> set(compact('manufactures'));

	}

	/**
	 * This method will display the collectible edit view of what is being approved.
	 *
	 * This will compare the current version of the collectible to the one that is in the edit to see what is different.
	 *
	 * I am not sure this is the best solution in the end but it will at least tell me what is different at the time of approval.  This will however,
	 * not tell me exactly what the user changed...only what is different at the time...since I have time stamps of when the edits are this should be fine, however
	 * I might want to update in the future that I only store what is being changed and not the whole collectible.
	 *
	 */
	function admin_approval($editId = null, $collectibleEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $collectibleEditId && is_numeric($collectibleEditId)) {
			$this -> set('collectibleEditId', $collectibleEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$collectible = $this -> Collectible -> getEditForApproval($collectibleEditId);
				//TODO hack for now
				if (isset($collectible['Collectible']['series_id']) && !empty($collectible['Collectible']['series_id'])) {
					$fullSeriesPath = $this -> Collectible -> Series -> buildSeriesPathName($collectible['Collectible']['series_id']);
					$collectible['Collectible']['seriesPath'] = $fullSeriesPath;
				}
				if ($collectible) {
					$this -> set('collectible', $collectible);
				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}
		} else {
			$this -> redirect('/');
		}
	}

	function admin_approve($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($id && is_numeric($id) && isset($this -> request -> data['Approval']['approve'])) {
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible')));
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			$notes = $this -> request -> data['Approval']['notes'];
			//Approve
			if ($this -> request -> data['Approval']['approve'] === 'true') {
				if (!empty($collectible) && $collectible['Collectible']['status_id'] === '2') {
					$data = array();
					$data['Collectible'] = array();
					$data['Collectible']['id'] = $collectible['Collectible']['id'];
					$data['Collectible']['status_id'] = 4;
					$data['Revision']['action'] = 'P';
					$data['Revision']['user_id'] = $this -> getUserId();
					$data['Revision']['notes'] = $this -> request -> data['Approval']['notes'];
					if ($this -> Collectible -> saveAll($data, array('validate' => false))) {
						//Ugh need to get this again so I can get the Revision id
						$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute'))));
						//update with the new revision id
						if (isset($collectible['CollectiblesUpload']) && !empty($collectible['CollectiblesUpload'])) {

							$this -> Collectible -> CollectiblesUpload -> id = $collectible['CollectiblesUpload'][0]['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}

							$this -> Collectible -> CollectiblesUpload -> Upload -> id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> Upload -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}
							$this -> Collectible -> CollectiblesUpload -> Upload -> id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> Upload -> saveField('status_id', 4)) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}
						}

						if (isset($collectible['AttributesCollectible']) && !empty($collectible['AttributesCollectible'])) {
							foreach ($collectible['AttributesCollectible'] as $key => $value) {
								$this -> Collectible -> AttributesCollectible -> id = $value['id'];
								if (!$this -> Collectible -> AttributesCollectible -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the AttributesCollectible with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}
								$this -> Collectible -> AttributesCollectible -> Attribute -> id = $value['Attribute']['id'];
								if (!$this -> Collectible -> AttributesCollectible -> Attribute -> saveField('status_id', 4)) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the attribute with the status id of 4 (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}
								$this -> Collectible -> AttributesCollectible -> Attribute -> id = $value['Attribute']['id'];
								if (!$this -> Collectible -> AttributesCollectible -> Attribute -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the attribute with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}

							}

						}

						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADMIN_APPROVE_NEW, 'user' => $this -> getUser(), 'object' => $collectible, 'target' => $collectible, 'type' => 'Collectible')));

						$this -> __sendApprovalEmail(true, $collectible['User']['email'], $collectible['User']['username'], $collectible['Collectible']['name'], $collectible['Collectible']['id']);

						$this -> Session -> setFlash(__('The collectible was successfully approved.', true), null, null, 'success');
						$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
					} else {
						$this -> Session -> setFlash(__('There was a problem approving the collectible.', true), null, null, 'error');
						$this -> redirect(array('admin' => true, 'action' => 'view', $id), null, true);
					}
				} else {
					$this -> Session -> setFlash(__('The collectible has been approved already.', true), null, null, 'error');
					$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
				}
			} else {
				//fuck it, I am deleting it
				if ($this -> Collectible -> delete($collectible['Collectible']['id'], true)) {

					//If this fails oh well
					//TODO: This should be in some callback
					//Have to do this because we have a belongsTo relationship on Collectible, probably should be a hasOne, fix at some point
					$this -> Collectible -> Revision -> delete($collectible['Collectible']['revision_id']);
					//Have to do the same thing with Entity
					$this -> Collectible -> EntityType -> delete($collectible['Collectible']['entity_type_id']);
					$this -> __sendApprovalEmail(false, $collectible['User']['email'], $collectible['User']['username'], $collectible['Collectible']['name'], null, $notes);
					$this -> Session -> setFlash(__('The collectible was successfully denied.', true), null, null, 'success');
					$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
				} else {
					$this -> Session -> setFlash(__('There was a problem denying the collectible.', true), null, null, 'error');
					$this -> redirect(array('admin' => true, 'action' => 'view', $id), null, true);
				}

			}

		} else {
			$this -> Session -> setFlash(__('Invalid collectible.', true), null, null, 'error');
			$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
		}

	}

	function __sendApprovalEmail($approvedChange = true, $email = null, $username = null, $collectibleName = null, $collectileId = null, $notes = '') {
		$return = true;
		if ($email) {
			// Set data for the "view" of the Email
			$this -> set(compact('collectibleName'));
			$this -> set(compact('username'));
			$this -> set(compact('notes'));

			$cakeEmail = new CakeEmail('smtp');
			$cakeEmail -> emailFormat('both');
			$cakeEmail -> to($email);
			if ($approvedChange) {
				$cakeEmail -> template('add_approval', 'simple');
				$cakeEmail -> subject('Your submission has been successfully approved!');
			} else {
				$cakeEmail -> template('add_deny', 'simple');
				$cakeEmail -> subject('Oh no! Your submission has been denied.');
			}
			$cakeEmail -> viewVars(array('collectible_url' => 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId, 'collectibleName' => $collectibleName, 'notes' => $notes, 'username' => $username));
			$cakeEmail -> send();
		} else {
			$return = false;
		}

		return $return;
	}

}
?>

