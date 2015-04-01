<?php
App::uses('ParserFactory', 'Lib/Parser');
// App::import('Model', 'Collectible');
class ParserController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function parse() {
        $this->autoRender = false;
        
        $factory = new ParserFactory();
        $parser = $factory->getParser('http://www.sideshowtoy.com/collectibles/marvel-bruce-banner-hot-toys-902165/');
        
        $collectible = $parser->parse('http://www.sideshowtoy.com/collectibles/marvel-bruce-banner-hot-toys-902165/');
        // we will want to serialize the data returned and store it with the collectible so that we can display it if we cannot figure out how to
        // convert it to collectible format
        // then save
        
        debug($collectible);
        
        $this->loadModel('Collectible');
        // TEST: But this is how we will auto upload photos
        $this->loadModel('CollectiblesUpload');
        // $this->CollectiblesUpload->add(array('CollectiblesUpload' => array('collectible_id' => 3465), 'Upload' => array('url' => 'http://www.sideshowtoy.com/wp-content/uploads/2013/12/902165-product-feature.jpg')), $this->getUser());
        // $Collectible = new Collectible( );
        debug($this->Collectible->convertToModel($collectible));
    }
}
?>