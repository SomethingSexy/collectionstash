<?php
App::uses('Sanitize', 'Utility');
class LicensesController extends AppController {

	public $helpers = array('Html', 'Ajax', 'Minify.Minify');

	public function getLicenseData() {
		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$license = $this -> License -> LicensesManufacture -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id' => $this -> data['manufacture_id'], 'LicensesManufacture.license_id' => $this -> data['license_id'])));
			$hasSeries = $this -> License -> LicensesManufacture -> LicensesManufacturesSeries -> hasSeries($license['LicensesManufacture']['id']);
			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			/*
			 * At this point we are changing license data, so we need to reset the series. We will determine if there are
			 * any series for this license and return a flag so the UI knows it can add a series
			 */
			$data['data']['hasSeries'] = $hasSeries;
			$this -> set('aLicenseData', $data);
		} else {
			$this -> set('aLicenseData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

	public function add() {
		// $data = array('300', 'A Nightmare on Elm Street', 'Alien VS Predator', 'AVATAR', 'Bayonets and Barbed Wire', 'Brotherhood of Arms', 'Bruce Lee', 'Buffy the Vampire Slayer', 'Clash of the Titans', 'Nosferatu', 'Conan', 'DC Comics', 'Diablo III', 'Dinosauria', 'Disney', 'Elvira', 'Elvis', 'Fife and Drum', 'Fifth Element', 'Friday the 13th', 'G.I. Joe', 'God of War', 'Hellboy', 'Hitman', 'Indiana Jones', 'Jurassic Park', 'Live by the Sword', 'Marvel', 'New Line House of Horrors', 'Planet of the Apes', 'Predator', 'Prince of Persia', 'Rambo', 'Robocop', 'SAW', 'Scarface', 'Six Gun Legends', 'Species', 'Star Trek', 'Star Wars', 'Terminator', 'The Dead', 'The Fly', 'The Godfather', 'The Lord of the Rings', 'TMNT', 'Tomb Raider', 'Transformers', 'Universal Monsters', 'Vampirella', 'Venture Bros.', 'Warhammer 40k', 'World of Warcraft', 'X-Files', 'Trick r Treat', 'Scarface');
		//
		// foreach ($data as $key => $value) {
		//
		// $licenses = array();
		// $licenses['name'] = $value;
		// $licenses['collectible_count'] = 0;
		// $this -> License -> create();
		// $this -> License -> saveAll($licenses, array('validate'=> false));
		//
		// }

		// for ($i = 1; $i <= 56; $i++) {
			// $licenses = array();
			// $licenses['manufacture_id'] = 1;
			// $licenses['license_id'] = $i;
			// $this -> License -> LicensesManufacture -> create();
			// $this -> License -> LicensesManufacture -> saveAll($licenses, array('validate' => false));
		// }

	}

}
?>