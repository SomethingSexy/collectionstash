<?php
App::uses('Sanitize', 'Utility');
class LicensesController extends AppController {

    public $helpers = array('Html', 'Js', 'Minify');

    // public function add() {
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

    // }

    public function admin_list() {
        $this -> checkLogIn();
        $this -> checkAdmin();

        $licenses = $this -> License -> getLicenses();

        $this -> set(compact('licenses'));
    }

    public function admin_view($license_id = null) {
        $this -> checkLogIn();
        $this -> checkAdmin();
        if (isset($license_id) && is_numeric($license_id)) {
            $license = $this -> License -> find("first", array('conditions' => array('License.id' => $license_id), 'contain' => array('LicensesManufacture' => array('Manufacture'))));
            debug($license);
            $this -> set(compact('license'));
        } else {
            $this -> redirect("/");
        }
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
        }
    }

    public function admin_remove($license_id) {
        $this -> checkLogIn();
        $this -> checkAdmin();

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