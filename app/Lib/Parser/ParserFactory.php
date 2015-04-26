<?php
App::uses('Sideshow', 'Lib/Parser/Type');
App::uses('ThreeA', 'Lib/Parser/Type');
App::uses('GentleGiant', 'Lib/Parser/Type');
class ParserFactory extends Object
{
    
    public function __construct() {
        
        parent::__construct();
    }
    
    public function getParser($url) {
        $retVal = null;
        
        switch (true) {
            case stripos($url, "sideshowtoy.com/collectibles"):
                $retVal = new Sideshow();
                break;

            // case stripos($url, "worldofthreea.com/catalog"):
            //     $productArray = scrape_wo3a($url);
            //     break;

            // case stripos($url, "3dstudio.com"):
            //     $productArray = scrape_gentleGiant($url);
            //     break;
        }
        
        return $retVal;
    }
}
?>