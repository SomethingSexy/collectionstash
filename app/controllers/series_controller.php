<?php
App::import('Sanitize');
class SeriesController extends AppController {

	var $name = 'Series';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler');

	/**
	 * TODO update this so that it returns the series data in levels
	 */
	public function getSeriesData() {
		if ($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}

		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));

			$manufactureId = $this -> data['manufacture_id'];
			$licenseId = $this -> data['license_id'];
			if (isset($this -> data['series_id']) && !empty($this -> data['series_id'])) {
				$seriesId = $this -> data['series_id'];
			}

			if (isset($seriesId) && !empty($seriesId)) {
				$series = $this -> Series -> LicensesManufacturesSeries -> getSeriesLevels($manufactureId, $licenseId, $seriesId);
			} else {
				$series = $this -> Series -> LicensesManufacturesSeries -> getSeriesLevels($manufactureId, $licenseId, null);
			}

			/*
			 * If a series id is not added, then just grab the initial list.
			 *
			 * If one is passed in, then we need to do a path call and then loop through
			 * that path call and get the list at each level, including the one I am at
			 * to draw that.  Use the same level interface that we use for collectible types.
			 */
			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = $series;
			// $data['data']['specializedTypes'] = $specializedTypes;
			$this -> set('aSeriesData', $data);
		} else {
			$this -> set('aSeriesData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

 // function add() {
	//
	// $data['Series']['parent_id'] = '30';
	// $data['Series']['name'] = 'Blackest Night';
	// $this -> Series -> save($data);
	// $this -> render(false);
	// }
}
?>