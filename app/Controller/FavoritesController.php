<?php
class FavoritesController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function index() {
        $this->autoRender = false;

        debug($this->Favorite -> find('first', array('conditions'=> array('Favorite.id'=> 1))));
        debug($this->Favorite -> find('first', array('conditions'=> array('Favorite.user_id'=> 1))));
    }
}
?>