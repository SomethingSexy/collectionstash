<?php
App::import('Sanitize');
class LicensesController extends AppController {

	var $name = 'Licenses';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	public function getLicenseData() {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}

		if(!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$license = $this -> License -> LicensesManufacture -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id'=> $this -> data['manufacture_id'], 'LicensesManufacture.license_id'=> $this -> data['license_id'] )));
			$series = $this -> License -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($license['LicensesManufacture']['id']);	
			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['series'] = $series;
			$this -> set('aLicenseData', $data);
		} else {
			$this -> set('aLicenseData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

}
?>