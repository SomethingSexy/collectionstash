<?php
App::uses('ParserFactory', 'Lib/Parser');
class ParserController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function parse() {
    	$this-> autoRender = false;

    	$factory = new ParserFactory();
    	$parser = $factory -> getParser('http://www.sideshowtoy.com/collectibles/dc-comics-man-of-steel-superman-sideshow-collectibles-300351/');

    	debug($parser -> parse('http://www.sideshowtoy.com/collectibles/dc-comics-man-of-steel-superman-sideshow-collectibles-300351/'));
    }
}
?>