<?php
require_once (dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'sunra' . DS . 'php-simple-html-dom-parser' . DS . 'Src' . DS . 'Sunra' . DS . 'PhpSimple' . DS . 'HtmlDomParser.php');
App::uses('Parsable', 'Lib/Parser');
App::uses('ParserUtility', 'Lib/Parser');
App::uses('CollectibleObj', 'Lib');
class Sideshow implements Parsable
{
    
    public function __construct() {
    }
    
    public function parse($url) {
        
        // this is only available for NEW non-archived products - dont use it right now
        //$ecommerce_text = get_HTML_SubString ($html, "ecommerceDetailItem : {","}");
        //$ecommerce_text = trim(preg_replace("/\s+/", " ", $ecommerce_text));
        
        $itemArray = array();
        
        // check productScraper function for full list of key names
        
        if ($html = ParserUtility::capture_url($url)) {
            
            //it seems all products have an id product on there, if that doesn't exist, fail fast
            if (!$html->find('div[id=product]')) {
                return false;
            }
            
            $collectible = new CollectibleObj();
            
            $head = $html->find("head", 0);
            $body = $html->find("body", 0);
            
            //FEATURE IMAGE
            $featureimage = $head->find("meta[property='og:image']", 0)->getAttribute('content');
            
            if ($featureimage) {
                array_push($collectible->photos, $featureimage);
            }
            
            //KEYWORDS
            // $itemArray['keywords'] = ParserUtility::htmlentities2utf8($head->find("meta[property='og:keywords']", 0)->getAttribute('content'));
            
            //URL
            $collectible->url = $head->find("meta[property='og:url']", 0)->getAttribute('content');
            
            //NAME
            $collectible->name = ParserUtility::htmlentities2utf8($body->find("h1", 0)->innertext);
            
            // GALLERY IMAGES
            $images = ($body->find("div[id=gallery]", 0)->find("a"));
            
            foreach ($images as $value) {
                if (substr($value->href, 0, 1) == "/") {
                    array_push($collectible->photos, "http://www.sideshowtoy.com" . $value->href);
                    
                    //$value = "http://www.sideshowtoy.com" . $value->href;
                    
                    
                } 
                else {
                    array_push($collectible->photos, $value->href);
                    
                    // $value = $value->href;
                    
                    
                }
            }
            
            //GIANT IMAGE
            // $itemArray['giantimage'] = ParserUtility::get_HTML_SubString($body, "<!-- large, clipped png -->", "<!-- .col -->");
            // $itemArray['giantimage'] = str_get_html($itemArray['giantimage']);
            // $itemArray['giantimage'] = $itemArray['giantimage']->find("img", 0)->src;
            // $itemArray['giantimage'] = ParserUtility::get_HTML_SubString($itemArray['giantimage'], ".com/");
            // $itemArray['giantimage'] = "http://www.sideshowtoy.com/" . $itemArray['giantimage'];
            
            // DESCRIPTION - Main product description - PRODUCT SUMMARY section - quick and dirty
            $description = $body->find("div[id=prod-summary]", 0)->innertext;
            $description = ParserUtility::htmlentities2utf8($description);
            
            $description = preg_replace("#<br\s*/?>#i", "\r\n", $description);
            
            // replace html breaks with newline
            
            $description = str_ireplace("<p>", "", $description);
            $description = str_ireplace("</p>", "\r\n\r\n", $description);
            
            // replace end paragraphs with double newline
            
            $description = str_ireplace("<div>", "", $description);
            $description = str_ireplace("</div>", "\r\n", $description);
            $description = trim(strip_tags($description));
            $collectible->description = $description;
            
            // Description additonal details - WHAT'S IN THE BOX section - quick and dirty
            // This will eventually be parts?
            $inBox = $body->find("div[id=in-box]", 0);
            if ($inBox) {
                $itemArray['description_details'] = $inBox->innertext;
                $itemArray['description_details'] = ParserUtility::htmlentities2utf8($itemArray['description_details']);
                
                $itemArray['description_details'] = trim(substr($itemArray['description_details'], stripos($itemArray['description_details'], "</h4>") + 5));
                
                $itemArray['description_details'] = str_ireplace("<p>", "", $itemArray['description_details']);
                $itemArray['description_details'] = str_ireplace("</p>", "\r\n", $itemArray['description_details']);
                
                $itemArray['description_details'] = str_ireplace("</li>", "\r\n", $itemArray['description_details']);
                $itemArray['description_details'] = str_ireplace("<li>", "* ", $itemArray['description_details']);
                $itemArray['description_details'] = trim(strip_tags($itemArray['description_details']));
            }
            
            // PRICE - easiest/most consistent way to grab price for current and archived products
            // Need to check to see if there is a sale, if so grab MSRP and not the sale price
            $cost = ParserUtility::get_HTML_SubString($body, 'price: "$', '"');
            $collectible->cost = floatval($cost);
            
            // UPC - sanitize a bit - only numbers, only up to 13 digits
            $collectible->upc = substr(preg_replace("/\D/", "", ParserUtility::sscProductDetails($body, 'upc')), 0, 12);
            
            // DIMENSIONS - Sideshow are very consistent with the format
            // there's some repetition in the code, but not worth replacing with a function
            $size = ParserUtility::sscProductDetailsHTML($body, 'size');
            
            // split into multiple lines if there are dimension sets for more than one part of this product (example: product + stand)
            $size = explode("<hr/>", $size);
            
            // scoop up all other dimension sets as plain text
            for ($i = 1, $len = count($size); $i < $len; ++$i) {
                $tempExtraDesc = $tempExtraDesc . strip_tags($size[$i]);
                if ($i != $len) $tempExtraDesc = $tempExtraDesc . "\r\n";
            }
            
            // first set of dimensions are for the product itself, so this is what we use
            $size = strip_tags($size[0]);
            
            // add the exra dimensions to the end of the description details block
            if (isset($tempExtraDesc)) {
                $tempExtraDesc = str_replace(":", ":\r\n", $tempExtraDesc);
                $itemArray['description_details'] = trim($itemArray['description_details'] . "\r\n\r\n" . $tempExtraDesc);
            }
            
            // split apart main dimensions to populate H, W, L D variables
            $tempSizeArray = explode("x", $size);
            
            foreach ($tempSizeArray as $value) {
                
                if (stripos($value, "h") && empty($collectible->height)) {
                    $collectible->height = floatval(trim(substr($value, 0, strpos($value, '"'))));
                } 
                else if (stripos($value, "w") && empty($collectible->width)) {
                    $collectible->width = floatval(trim(substr($value, 0, strpos($value, '"'))));
                } 
                else if (stripos($value, "l") && empty($collectible->height)) {
                    $collectible->height = floatval(trim(substr($value, 0, strpos($value, '"'))));
                } 
                else if (stripos($value, "d") && empty($collectible->depth)) {
                    $collectible->depth = floatval(trim(substr($value, 0, strpos($value, '"'))));
                } 
                else if (empty($collectible->height)) {
                    $collectible->height = trim(substr($value, 0, strpos($value, '"')));
                }
            }
            
            // WEIGHT - splits whole line on "lbs" and keeps the first element
            $weight = ParserUtility::sscProductDetails($body, 'Product Weight');
            $weight = explode("lbs", $weight);
            $weight = floatval($weight[0]);
            $collectible->weight = $weight;
            
            // MANUFACTURER
            $collectible->manufacturer = ParserUtility::sscProductDetails($body, 'manufacturer');
            
            // SERIES - header3 content - sometimes used for license name, otherwise used for sub-license/series
            $collectible->series = trim(ParserUtility::htmlentities2utf8($body->find("h3", 0)->innertext));
            
            // LICENSE - pre-populate license with series content just in case the license is blank
            // $itemArray['license'] = $itemArray['series'];
            
            if ($var = ParserUtility::sscProductDetails($body, 'license')) {
                $collectible->brand = $var;
            } 
            else {
                $collectible->brand = $collectible->series;
            }
            
            // SKU
            $collectible->productCode = ParserUtility::sscProductDetails($body, 'sku');
            
            // EDITION
            $itemArray['editionsize'] = ParserUtility::get_HTML_SubString($body, "<!-- .labels -->", "<!-- .col -->");
            if (stristr($itemArray['editionsize'], "limited")) {
                $itemArray['limitededition'] = true;
                $itemArray['editionsize'] = str_ireplace("limited edition:", "", $itemArray['editionsize']);
                $itemArray['editionsize'] = str_ireplace("limited edition", "", $itemArray['editionsize']);
                $itemArray['editionsize'] = str_ireplace("&nbsp;", "", $itemArray['editionsize']);
                $itemArray['editionsize'] = trim(strip_tags($itemArray['editionsize']));
            } 
            else {
                $itemArray['editionsize'] = "";
            }
            
            // ARTISTS
            $tempHTML = str_get_html(ParserUtility::get_HTML_SubString($body, "<!-- artists -->", "<!-- authors -->"));
            
            // artists as HTML list items
            if ($itemArray['artists'] = $tempHTML->find("li")) {;
                
                foreach ($itemArray['artists'] as & $value) {
                    $value = ParserUtility::htmlentities2utf8($value);
                    $value = trim(preg_replace("/[\[{\(].*[\]}\)]/U", "", strip_tags($value)));
                    
                    // remove HTML and bracketed content from artist names
                    
                    if ($cleaned = ParserUtility::get_HTML_SubString($value, " by ")) {
                        
                        // trim pre-text if it contains credits (head paintd by artist name)
                        $value = $cleaned;
                    }
                }
                unset($value);
                
                // artists as plain text separated by commas - just hammer this out with substitutions to clean it up
                
                
            } 
            else {
                
                $tempHTML = ParserUtility::htmlentities2utf8($tempHTML);
                $tempHTML = str_ireplace("artists", "", $tempHTML);
                $tempHTML = str_ireplace("artist", "", $tempHTML);
                $tempHTML = str_ireplace(":", "", $tempHTML);
                $tempHTML = preg_replace("/[\[{\(].*[\]}\)]/U", "", strip_tags($tempHTML));
                
                // remove bracketed content (and strip HTML tags)
                
                $itemArray['artists'] = explode(",", trim($tempHTML));
                
                foreach ($itemArray['artists'] as & $value) {
                    $value = trim($value);
                }
                unset($value);
            }
            $itemArray['artists'] = array_filter($itemArray['artists']);
            
            // don't keep any blanks
            
            // YEAR
            // pre-populate from copyright date if available
            $itemArray['copyright'] = ParserUtility::get_HTML_SubString($body, "<!-- legal -->", "<!-- .details -->");
            $itemArray['copyright'] = ParserUtility::htmlentities2utf8(strip_tags(preg_replace("/\s+/", " ", $itemArray['copyright'])));
            
            // grab the highest number year from the shipping date for pre-order products
            if ($itemArray['shipyear'] = ParserUtility::sscProductDetails($body, 'ship')) {
                
                // grab all years from ship date (in case of range) and use the largest as the initial date of the product
                preg_match_all('/(\d{4})/', $itemArray['shipyear'], $allYears);
                
                $itemArray['shipyear'] = "";
                foreach ($allYears[0] as $value) {
                    if ($value > $itemArray['shipyear']) $itemArray['shipyear'] = $value;
                }
            }
            
            // grab all years from copyright and compare the largest against previous grabbed year numbers
            preg_match_all('/(\d{4})/', $itemArray['copyright'], $allYears);
            
            foreach ($allYears[0] as $value) {
                if (isset($itemArray['orderyear']) && $value > $itemArray['orderyear']) {
                    $itemArray['orderyear'] = $value;
                }
            }
            
            // if all else fails, grab the year from the feature image folder path
            $imgURLparts = explode('/', $featureimage);
            
            $imgYear = $imgURLparts[count($imgURLparts) - 3];
            
            if (strlen($imgYear) == 4 && $imgYear < 2050 && $imgYear > 1990) $itemArray['imgyear'] = $imgYear;
            
            // use the img path date as the order year if greater than existing order year (copyright)
            
            if (!isset($itemArray['orderyear']) || $itemArray['orderyear'] < $itemArray['imgyear']) {
                $itemArray['orderyear'] = $itemArray['imgyear'];
            }
            
            // OEM Scale
            $OEMscale = ParserUtility::sscProductDetails($body, 'scale');
            
            //PRODUCT TYPE
            // Assign product type and scale as follows - done this way to allow substring matching within scale text
            // This can be compacted by re-organising and not breaking on certain matches - too difficult to maintain that way
            switch (true) {
                case stristr($OEMscale, "figure stand"):
                case stristr($OEMscale, "display stage"):
                case stristr($OEMscale, "display case"):
                    $collectible->type = "action figure accessory";
                    break;

                case stristr($OEMscale, "sixth scale figure related product"):
                    $collectible->scale = "1/6";
                    $collectible->type = "action figure accessory";
                    break;

                case stristr($OEMscale, "maquette"):
                    $collectible->type = "maquette";
                    break;

                case stristr($OEMscale, "sixth scale"):
                    $collectible->scale = "1/6";
                    $collectible->type = "action figure";
                    break;

                case stristr($OEMscale, "quarter scale"):
                    $collectible->scale = "1/4";
                    $collectible->type = "action figure";
                    break;

                case stristr($OEMscale, "premium scale collectible figure"):
                case stristr($OEMscale, "collectible figure"):
                case stristr($OEMscale, "collectible set"):
                    $collectible->type = "action figure";
                    break;

                case stristr($OEMscale, "bust"):
                    $collectible->type = "bust";
                    break;

                case stristr($OEMscale, "premium format"):
                    $collectible->scale = "1/4";
                    $collectible->type = "statue";
                    break;

                case stristr($OEMscale, "life-size"):
                    $collectible->scale = "1/1";
                    $collectible->type = "statue";
                    break;

                case stristr($OEMscale, "legendary scale"):
                case stristr($OEMscale, "statue"):
                    $collectible->type = "statue";
                    break;

                case stristr($OEMscale, "vinyl"):
                    $collectible->type = "vinyl figure";
                    break;

                case stristr($OEMscale, "diorama"):
                    $collectible->type = "diorama";
                    break;

                case stristr($OEMscale, "prop replica"):
                    $collectible->scale = "1/1";
                    $collectible->type = "prop replica";
                    break;

                case stristr($OEMscale, "replica"):
                    $collectible->type = "replica";
                    break;

                case stristr($OEMscale, "apparel"):
                    $collectible->scale = "1/1";
                    $collectible->type = "apparel";
                    break;

                case stristr($OEMscale, "model kit"):
                    $collectible->type = "model kit";
                    break;

                case stristr($OEMscale, "art print"):
                    $collectible->type = "print";
                    break;

                case stristr($OEMscale, "book"):
                    $collectible->scale = "1/1";
                    $collectible->type = "book";
                    break;
            }
            
            // EXCLUSIVE
            if ($body->find("a[class=label-exclusive]", 0)) {
                $itemArray['exclusive'] = true;
            }
            
            return $collectible;
        } 
        else {
            
            // end if the URL was read
            
            return 0;
        }
    }
} ?>