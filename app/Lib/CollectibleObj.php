<?php
// had to name this CollectibleType since it was interferring with the model
class CollectibleObj
{
    // property declaration
    public $name;
    public $manufacturer;
    public $type;
    public $series;
    public $description;
    public $msrp;
    public $editionSize;
    public $upc;
    public $width;
    public $height;
    public $depth;
    public $weight;
    public $brand;
    public $variant = false;
    public $url;
    public $exclusive = false;
    public $scale;
    public $releaseYear;
    public $limited = false;
    public $productCode;
    public $numbered = false;
    public $numberOfPieces;
    public $signed = false;
    public $official = true;
    public $photos = array();
    public $cost;
}
?>