<?php
class Series extends AppModel {
	var $name = 'Series';
	var $useTable = 'series';
	var $hasMany = array('Collectible', 'LicensesManufacturesSeries');
	var $actsAs = array('Tree','Containable');
	
	/**
	 * This method, given a series id will build the series name path.
	 * 
	 * This means that it will take the hierarchy of the path and build out
	 * the name representation
	 */
	public function buildSeriesPathName($seriesId){	
		$paths = $this -> getPath($seriesId);
		$seriesPathName = '';
		$totalPaths = count($paths);
		foreach ($paths as $key => $value) {
			$seriesPathName .= $value['Series']['name'];
			if(++$key != $totalPaths){
				$seriesPathName	.= '/';
			}
		}
			
		return $seriesPathName;
	}

}
?>
