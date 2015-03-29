<?php
App::uses('ParserFactory', 'Lib/Parser');
class ParserController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function parse() {
    	$this-> autoRender = false;

    	$factory = new ParserFactory();
    	$parser = $factory -> getParser('http://www.sideshowtoy.com/collectibles/dc-comics-man-of-steel-superman-sideshow-collectibles-300351/');

    	$collectible = $parser -> parse('http://www.sideshowtoy.com/collectibles/dc-comics-man-of-steel-superman-sideshow-collectibles-300351/');
    	// we will want to serialize the data returned and store it with the collectible so that we can display it if we cannot figure out how to 
    	// convert it


    	debug();
    }
}
?>