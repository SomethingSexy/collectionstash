<?php
App::uses('ParserFactory', 'Lib/Parser');
// App::import('Model', 'Collectible');
class ParserController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function parse() {
        $this->autoRender = false;
        
        $factory = new ParserFactory();
        $parser = $factory->getParser('http://www.sideshowtoy.com/collectibles/marvel-bruce-banner-hot-toys-902165/');
        
        $parsedCollectible = $parser->parse('http://www.sideshowtoy.com/collectibles/marvel-bruce-banner-hot-toys-902165/');
        // we will want to serialize the data returned and store it with the collectible so that we can display it if we cannot figure out how to
        // convert it to collectible format
        // then save
        
        debug($parsedCollectible);
        
        $this->loadModel('Collectible');
        // TEST: But this is how we will auto upload photos
        $this->loadModel('CollectiblesUpload');
        // $Collectible = new Collectible( );
        $collectibleModel = $this->Collectible->convertToModel($parsedCollectible);
        debug($collectibleModel);
        
        $collectible = $this->Collectible->createInitial(false, false, $this->getUserId());
        $collectibleId = $collectible['response']['data']['id'];
        // now upload all of the photos
        if (!empty($collectibleModel['CollectiblesUpload'])) {
            foreach ($collectibleModel['CollectiblesUpload'] as $key => $upload) {
                $this->CollectiblesUpload->add(array('CollectiblesUpload' => array('collectible_id' => $collectibleId), 'Upload' => array('url' => $upload['Upload']['url'])), $this->getUser());
            }
            unset($collectibleModel['CollectiblesUpload']);
        }
        
        debug($collectible);
        
        $collectibleModel['Collectible']['id'] = $collectibleId;
        $this->Collectible->saveCollectible($collectibleModel, $this->getUser());
    }
}
?>