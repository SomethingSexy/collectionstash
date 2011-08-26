<?php
class CollectibleEdit extends AppModel {
	var $name = 'CollectibleEdit';
	var $actsAs = array('Containable');

	var $belongsTo = array('Manufacture' => array('className' => 'Manufacture', 'foreignKey' => 'manufacture_id'), 'Collectibletype' => array('className' => 'Collectibletype', 'foreignKey' => 'collectibletype_id'), 'License' => array('className' => 'License', 'foreignKey' => 'license_id'), 'Series' => array('className' => 'Series', 'foreignKey' => 'series_id'), 'Scale');
	function beforeSave() {
		//Update Edition Size stuff
		$editionSize = $this -> data['CollectibleEdit']['edition_size'];
		$limited = $this -> data['CollectibleEdit']['edition_size'];

		if(trim($editionSize) != '' && !$limited) {
			$editionSize = '';
		}

		//For whatever reason, cakephp year the put another array under the field
		if(isset($this -> data['CollectibleEdit']['release']['year'])) {
			$year = $this -> data['CollectibleEdit']['release']['year'];
			$this -> data['CollectibleEdit']['release'] = $year;
		}

		//Check to see if these are set, if they are not, default them to false
		if(!isset($this -> data['CollectibleEdit']['exclusive'])) {
			$this -> data['CollectibleEdit']['exclusive'] = 0;
		}
		if(!isset($this -> data['CollectibleEdit']['variant'])) {
			$this -> data['CollectibleEdit']['variant'] = 0;
		}

		$this -> data['CollectibleEdit']['msrp'] = str_replace('$', '', $this -> data['CollectibleEdit']['msrp']);
		$this -> data['CollectibleEdit']['msrp'] = str_replace(',', '', $this -> data['CollectibleEdit']['msrp']);

		return true;
	}
}
?>
