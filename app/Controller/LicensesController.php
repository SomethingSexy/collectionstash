<?php
App::uses('Sanitize', 'Utility');
class LicensesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function admin_list() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$licenses = $this -> License -> getLicenses();

		$this -> set(compact('licenses'));

		$this -> layout = 'fluid';
	}

	public function admin_view($license_id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if (isset($license_id) && is_numeric($license_id)) {
			$license = $this -> License -> find("first", array('conditions' => array('License.id' => $license_id), 'contain' => array('LicensesManufacture' => array('Manufacture'))));

			$this -> set(compact('license'));
		} else {
			$this -> redirect("/");
		}

		$this -> layout = 'fluid';
	}

	public function admin_add() {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			if (!empty($this -> request -> data)) {
				$this -> request -> data = Sanitize::clean($this -> request -> data);
				if ($this -> License -> save($this -> request -> data)) {
					$this -> Session -> setFlash(__('The license was successfully added.', true), null, null, 'success');
					$this -> redirect(array('action' => 'view', $this -> License -> id));
				} else {
					$this -> Session -> setFlash(__('There was a problem adding this license.', true), null, null, 'error');
				}
			}
		} else {

		}
		
		$this -> layout = 'fluid';

	}

	public function admin_edit($license_id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			if (!empty($this -> request -> data)) {
				$this -> request -> data = Sanitize::clean($this -> request -> data);
				if ($this -> License -> save($this -> request -> data)) {
					$this -> Session -> setFlash(__('The license was successfully updated.', true), null, null, 'success');
					$this -> redirect(array('action' => 'view', $this -> License -> id));
				} else {
					$this -> Session -> setFlash(__('There was a problem updating this license.', true), null, null, 'error');
				}
			}
		} else {
			if (!isset($license_id) && !is_numeric($license_id)) {
				$this -> redirect(array('action' => 'list'));
				return;
			}
			$license = $this -> License -> find("first", array('conditions' => array('License.id' => $license_id), 'contain' => false));
			if (empty($license)) {
				$this -> redirect(array('action' => 'list'));
				return;
			}
			$this -> request -> data = $license;

			$this -> layout = 'fluid';
		}
	}

	public function admin_remove($license_id) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$this -> layout = 'fluid';

		if (!is_null($license_id) && is_numeric($license_id)) {
			$license = $this -> License -> find("first", array('conditions' => array('License.id' => $license_id), 'contain' => false));
			if (isset($license) && !empty($license)) {
				if ($this -> License -> delete($license_id)) {
					$this -> Session -> setFlash(__('The license has been successfully removed.', true), null, null, 'success');
					$this -> redirect(array('action' => 'list'), null, true);
				} else {
					$this -> Session -> setFlash(__('There was a problem removing the license.', true), null, null, 'error');
					$this -> redirect(array('action' => 'view', $license_id), null, true);
				}
			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
			}
		} else {
			$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
		}

		$this -> redirect(array('action' => 'list'), null, true);
	}

}
?>