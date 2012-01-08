<?php
App::uses('Sanitize', 'Utility');
class ManufacturesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');
	
	public function getManufactureData() {
		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			//Grab all licenses for this manufacture
			$licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesByManufactureId($this -> data['id']);
			reset($licenses);
			$this -> set(compact('licenses'));
			$firstLic = key($licenses);
			debug($firstLic);

			$license = $this -> Manufacture -> LicensesManufacture -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id' => $this -> data['id'], 'LicensesManufacture.license_id' => $firstLic)));
			//Grab all series for this license...should I just return all for all licenses and send that down the request?
			$series = $this -> Manufacture -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($license['LicensesManufacture']['id']);

			//grab all collectible types for this manufacture
			$collectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($this -> data['id']);
			//As long as collectible types isn't empty, grab any manufacturer specific types
			$specializedTypes = array();
			if (!empty($collectibletypes)) {
				reset($collectibletypes);
				$firstColType = key($collectibletypes);

				$specializedTypes = $this -> Manufacture -> CollectibletypesManufacture -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($this -> data['id'], $firstColType);
			}

			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['licenses'] = $licenses;
			$data['data']['types'] = $collectibletypes;
			$data['data']['series'] = $series;
			$data['data']['specializedTypes'] = $specializedTypes;
			$this -> set('aManufactureData', $data);
		} else {
			$this -> set('aManufactureData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

	public function view($id = null) {
		if (isset($id) && is_numeric($id)) {
			$manufacture = $this -> Manufacture -> find("first", array('conditions' => array('Manufacture.id' => $id), 'contain' => false));
			if (!empty($manufacture)) {
				//TODO: update this to use counter cache once I can add from the app
				$licenseCount = $this -> Manufacture -> LicensesManufacture -> getLicenseCount($id);
				$manufacture['Manufacture']['license_count'] = $licenseCount;
				$collectibletypeCount = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibletypeCount($id);
				$manufacture['Manufacture']['collectibletype_count'] = $collectibletypeCount;
				$totalCollectibles = $this -> Manufacture -> Collectible -> getCollectibleCount();

				if (is_numeric($totalCollectibles) && $totalCollectibles > 0) {
					$manCollectibleCount = $manufacture['Manufacture']['collectible_count'];
					$percentage = round($manCollectibleCount * 100 / $totalCollectibles) . '%';
					$manufacture['Manufacture']['percentage_of_total'] = $percentage;
				}

				$licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesByManufactureId($id);
				$this -> set(compact('licenses'));

				$manufacturerCollectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> find('all', array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $id)));

				$highestPriceCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'desc'), 'contain' => array('Manufacture')));
				$lowestPriceCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'asc'), 'contain' => array('Manufacture')));
				$manufacture['Manufacture']['highest_price'] = $highestPriceCollectible['Collectible']['msrp'];
				$manufacture['Manufacture']['lowest_price'] = $lowestPriceCollectible['Collectible']['msrp'];

				$lowestEditionCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'asc'), 'contain' => array('Manufacture')));
				$highestEditionCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'desc'), 'contain' => array('Manufacture')));
				if (!empty($lowestEditionCollectible)) {
					$manufacture['Manufacture']['lowest_edition_size'] = $lowestEditionCollectible['Collectible']['edition_size'];
				}
				if (!empty($highestEditionCollectible)) {
					$manufacture['Manufacture']['highest_edition_size'] = $highestEditionCollectible['Collectible']['edition_size'];
				}

				$this -> set(compact('manufacture'));
			} else {
				$this -> redirect("/");
			}

		} else {
			$this -> redirect("/");
		}
	}

}
?>