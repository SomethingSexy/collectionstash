<?php
App::import('Sanitize');
class ManufacturesController extends AppController {

	var $name = 'Manufactures';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	public function getManufactureData() {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}

		if(!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			//Grab all licenses for this manufacture
			$licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesByManufactureId($this -> data['id']);
			reset($licenses);
			$this -> set(compact('licenses'));
			$firstLic = key($licenses);
			debug($firstLic);
			
			$license = $this -> Manufacture -> LicensesManufacture -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id'=> $this -> data['id'], 'LicensesManufacture.license_id'=> $firstLic )));
			//Grab all series for this license...should I just return all for all licenses and send that down the request?
			$series = $this -> Manufacture -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($license['LicensesManufacture']['id']);		
			
			//grab all collectible types for this manufacture
			$collectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($this -> data['id']);
				
			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['licenses'] = $licenses;
			$data['data']['types'] = $collectibletypes;
			$data['data']['series'] = $series;
			$this -> set('aManufactureData', $data);
		} else {
			$this -> set('aManufactureData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

}
?>