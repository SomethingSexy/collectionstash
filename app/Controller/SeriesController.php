<?php
App::uses('Sanitize', 'Utility');
class SeriesController extends AppController {

    public $helpers = array('Html', 'Js', 'Minify', 'Tree');
    /**
     * TODO update this so that it returns the series data in levels
     */
    public function getSeriesData() {
        if (!empty($this -> data)) {
            $this -> data = Sanitize::clean($this -> data, array('encode' => false));

            $manufactureId = $this -> data['manufacture_id'];
            $licenseId = $this -> data['license_id'];
            if (isset($this -> data['series_id']) && !empty($this -> data['series_id'])) {
                $seriesId = $this -> data['series_id'];
            }

            if (isset($seriesId) && !empty($seriesId)) {
                $series = $this -> Series -> Manufacture -> getSeriesLevels($manufactureId, $seriesId);
            } else {
                $series = $this -> Series -> Manufacture -> getSeriesLevels($manufactureId, null);
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

    public function admin_list() {
        $stuff = $this -> Series -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id'), 'order' => 'lft ASC'));
        $this -> set('stuff', $stuff);
    }

    public function admin_add() {

        $invalidRequest = false;
        $invalidSave = false;
        $invalidPost = false;
        $isSuccess = false;
        if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
            //Make sure it a post, if not don't accept it
            if ($this -> request -> is('post')) {
                if (!empty($this -> request -> data)) {
                    if ($this -> Series -> save($this -> request -> data)) {
                        $isSuccess = true;
                       
                    } else {
                        $invalidSave = true;
                    }
                } else {
                    $invalidPost = true;
                }
            } else {
                $invalidPost = true;
            }
        } else {
            $invalidRequest = true;

        }
        if ($this -> request -> isAjax()) {
            $data = array();
            if ($invalidSave) {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'] = array($this -> Series -> validationErrors);
            } else if ($invalidPost) {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
            } else if ($invalidRequest) {
                //If they are not logged in and are trying to access this then just time them out
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = true;
                $data['data'] = array();
            } else {
                //successful
                $data['success'] = array('isSuccess' => true);
                $data['isTimeOut'] = false;
                $data['data'] = array('id' => $this -> Series -> id);
            }
            //better way to handle this?
            $this -> set('aSeriesData', $data);
            $this -> render('admin_add_ajax');
        } else {
            if ($isSuccess) {
                $this -> redirect(array('action' => 'list'));
            }
        }

    }

    public function admin_remove() {
        $data = array();
        if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
            if (!empty($this -> request -> data) && $this -> request -> is('post')) {
                $this -> Series -> id = $this -> request -> data['Series']['id'];
                if ($this -> Series -> delete()) {
                    $data['success'] = array('isSuccess' => true);
                    $data['isTimeOut'] = false;
                    $data['data'] = array();

                } else {
                    $data['success'] = array('isSuccess' => false);
                    $data['isTimeOut'] = false;
                    $data['errors'] = array($this -> Series -> validationErrors);
                }
            } else {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
            }
        } else {
            $data['success'] = array('isSuccess' => false);
            $data['isTimeOut'] = false;
            $data['data'] = array();
            $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
        }

        $this -> set('aSeriesData', $data);
    }

    // function add() {
    // $data['Series']['parent_id'] = '224';
    // $data['Series']['name'] = 'Winson Classic Creation';
    // $this -> Series -> save($data);
//     
    // // $this -> Series -> id = 190;
    // // $this -> Series -> delete();
//     
    // $this -> render(false);
    // }

}
?>