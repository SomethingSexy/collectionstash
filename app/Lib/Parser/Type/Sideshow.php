<?php
require_once (dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'sunra' . DS . 'php-simple-html-dom-parser' . DS . 'Src' . DS . 'Sunra' . DS . 'PhpSimple' . DS . 'HtmlDomParser.php');
App::uses('Parsable', 'Lib/Parser');
App::uses('ParserUtility', 'Lib/Parser');
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
            
            $head = $html->find("head", 0);
            $body = $html->find("body", 0);
            
            //FEATURE IMAGE
            $itemArray['featureimage'] = $head->find("meta[property='og:image']", 0)->getAttribute('content');
            
            //KEYWORDS
            $itemArray['keywords'] = ParserUtility::htmlentities2utf8($head->find("meta[property='og:keywords']", 0)->getAttribute('content'));
            
            //URL
            $itemArray['url'] = $head->find("meta[property='og:url']", 0)->getAttribute('content');
            
            //NAME
            $itemArray['name'] = ParserUtility::htmlentities2utf8($body->find("h1", 0)->innertext);
            
            // GALLERY IMAGES
            $tempHTML = $body->find("div[id=gallery]", 0);
            $itemArray['galleryimages'] = ($tempHTML->find("a"));
            unset($tempHTML);
            
            foreach ($itemArray['galleryimages'] as & $value) {
                if (substr($value->href, 0, 1) == "/") $value = "http://www.sideshowtoy.com" . $value->href;
                else $value = $value->href;
            }
            unset($value);
            
            //GIANT IMAGE
            $itemArray['giantimage'] = ParserUtility::get_HTML_SubString($body, "<!-- large, clipped png -->", "<!-- .col -->");
            $itemArray['giantimage'] = str_get_html($itemArray['giantimage']);
            $itemArray['giantimage'] = $itemArray['giantimage']->find("img", 0)->src;
            $itemArray['giantimage'] = ParserUtility::get_HTML_SubString($itemArray['giantimage'], ".com/");
            $itemArray['giantimage'] = "http://www.sideshowtoy.com/" . $itemArray['giantimage'];
            
            // DESCRIPTION - Main product description - PRODUCT SUMMARY section - quick and dirty
            $itemArray['description'] = $body->find("div[id=prod-summary]", 0)->innertext;
            $itemArray['description'] = ParserUtility::htmlentities2utf8($itemArray['description']);
            
            $itemArray['description'] = preg_replace("#<br\s*/?>#i", "\r\n", $itemArray['description']);
            
            // replace html breaks with newline
            
            $itemArray['description'] = str_ireplace("<p>", "", $itemArray['description']);
            $itemArray['description'] = str_ireplace("</p>", "\r\n\r\n", $itemArray['description']);
            
            // replace end paragraphs with double newline
            
            $itemArray['description'] = str_ireplace("<div>", "", $itemArray['description']);
            $itemArray['description'] = str_ireplace("</div>", "\r\n", $itemArray['description']);
            $itemArray['description'] = trim(strip_tags($itemArray['description']));
            
            // Description additonal details - WHAT'S IN THE BOX section - quick and dirty
            $itemArray['description_details'] = $body->find("div[id=in-box]", 0)->innertext;
            $itemArray['description_details'] = ParserUtility::htmlentities2utf8($itemArray['description_details']);
            
            $itemArray['description_details'] = trim(substr($itemArray['description_details'], stripos($itemArray['description_details'], "</h4>") + 5));
            
            $itemArray['description_details'] = str_ireplace("<p>", "", $itemArray['description_details']);
            $itemArray['description_details'] = str_ireplace("</p>", "\r\n", $itemArray['description_details']);
            
            $itemArray['description_details'] = str_ireplace("</li>", "\r\n", $itemArray['description_details']);
            $itemArray['description_details'] = str_ireplace("<li>", "* ", $itemArray['description_details']);
            $itemArray['description_details'] = trim(strip_tags($itemArray['description_details']));
            
            // PRICE - easiest/most consistent way to grab price for current and archived products
            $itemArray['price'] = floatval(ParserUtility::get_HTML_SubString($body, 'price: "$', '"'));
            
            // UPC - sanitize a bit - only numbers, only up to 13 digits
            $itemArray['upc'] = preg_replace("/\D/", "", ParserUtility::sscProductDetails($body, 'upc'));
            $itemArray['upc'] = substr($itemArray['upc'], 0, 12);
            
            // DIMENSIONS - Sideshow are very consistent with the format
            // there's some repetition in the code, but not worth replacing with a function
            $itemArray['size'] = ParserUtility::sscProductDetailsHTML($body, 'size');
            
            // split into multiple lines if there are dimension sets for more than one part of this product (example: product + stand)
            $itemArray['size'] = explode("<hr/>", $itemArray['size']);
            
            // scoop up all other dimension sets as plain text
            for ($i = 1, $len = count($itemArray['size']); $i < $len; ++$i) {
                $tempExtraDesc = $tempExtraDesc . strip_tags($itemArray['size'][$i]);
                if ($i != $len) $tempExtraDesc = $tempExtraDesc . "\r\n";
            }
            
            // first set of dimensions are for the product itself, so this is what we use
            $itemArray['size'] = strip_tags($itemArray['size'][0]);
            
            // add the exra dimensions to the end of the description details block
            if (isset($tempExtraDesc)) {
                $tempExtraDesc = str_replace(":", ":\r\n", $tempExtraDesc);
                $itemArray['description_details'] = trim($itemArray['description_details'] . "\r\n\r\n" . $tempExtraDesc);
            }
            
            // split apart main dimensions to populate H, W, L D variables
            $tempSizeArray = explode("x", $itemArray['size']);
            
            foreach ($tempSizeArray as $value) {
                
                if (stripos($value, "h") && empty($itemArray['height'])) {
                    $itemArray['height'] = trim(substr($value, 0, strpos($value, '"')));
                    $itemArray['height'] = floatval($itemArray['height']);
                } 
                elseif (stripos($value, "w") && empty($itemArray['width'])) {
                    $itemArray['width'] = trim(substr($value, 0, strpos($value, '"')));
                    $itemArray['width'] = floatval($itemArray['width']);
                } 
                elseif (stripos($value, "l") && empty($itemArray['length'])) {
                    $itemArray['length'] = trim(substr($value, 0, strpos($value, '"')));
                    $itemArray['length'] = floatval($itemArray['length']);
                } 
                elseif (stripos($value, "d") && empty($itemArray['depth'])) {
                    $itemArray['depth'] = trim(substr($value, 0, strpos($value, '"')));
                    $itemArray['depth'] = floatval($itemArray['depth']);
                } 
                elseif (empty($itemArray['height'])) {
                    $itemArray['height'] = trim(substr($value, 0, strpos($value, '"')));
                    $itemArray['height'] = floatval($itemArray['height']);
                }
            }
            unset($tempSizeArray);
            
            // OEM Scale
            $itemArray['OEMscale'] = ParserUtility::sscProductDetails($body, 'scale');
            
            // WEIGHT - splits whole line on "lbs" and keeps the first element
            $itemArray['weight'] = ParserUtility::sscProductDetails($body, 'Product Weight');
            $itemArray['weight'] = explode("lbs", $itemArray['weight']);
            $itemArray['weight'] = floatval($itemArray['weight'][0]);
            
            // MANUFACTURER
            $itemArray['manufacturer'] = ParserUtility::sscProductDetails($body, 'manufacturer');
            
            // SERIES - header3 content - sometimes used for license name, otherwise used for sub-license/series
            $itemArray['series'] = trim(ParserUtility::htmlentities2utf8($body->find("h3", 0)->innertext));
            
            // LICENSE - pre-populate license with series content just in case the license is blank
            $itemArray['license'] = $itemArray['series'];
            
            if ($var = ParserUtility::sscProductDetails($body, 'license')) {
                $itemArray['license'] = $var;
            }
            
            // SKU
            $itemArray['sku'] = ParserUtility::sscProductDetails($body, 'sku');
            
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
            $imgURLparts = explode('/', $itemArray['featureimage']);
            
            $imgYear = $imgURLparts[count($imgURLparts) - 3];
            
            if (strlen($imgYear) == 4 && $imgYear < 2050 && $imgYear > 1990) $itemArray['imgyear'] = $imgYear;
            
            // use the img path date as the order year if greater than existing order year (copyright)
            
            if (!isset($itemArray['orderyear']) || $itemArray['orderyear'] < $itemArray['imgyear']) {
                $itemArray['orderyear'] = $itemArray['imgyear'];
            }
            
            //PRODUCT TYPE
            // Assign product type and scale as follows - done this way to allow substring matching within scale text
            // This can be compacted by re-organising and not breaking on certain matches - too difficult to maintain that way
            switch (true) {
                case stristr($itemArray['OEMscale'], "figure stand"):
                case stristr($itemArray['OEMscale'], "display stage"):
                case stristr($itemArray['OEMscale'], "display case"):
                    $itemArray[type] = "action figure accessory";
                    break;

                case stristr($itemArray['OEMscale'], "sixth scale figure related product"):
                    $itemArray['scale'] = "1/6";
                    $itemArray['type'] = "action figure accessory";
                    break;

                case stristr($itemArray['OEMscale'], "maquette"):
                    $itemArray['type'] = "maquette";
                    break;

                case stristr($itemArray['OEMscale'], "sixth scale"):
                    $itemArray['scale'] = "1/6";
                    $itemArray['type'] = "action figure";
                    break;

                case stristr($itemArray['OEMscale'], "quarter scale"):
                    $itemArray['scale'] = "1/4";
                    $itemArray['type'] = "action figure";
                    break;

                case stristr($itemArray['OEMscale'], "premium scale collectible figure"):
                case stristr($itemArray['OEMscale'], "collectible figure"):
                case stristr($itemArray['OEMscale'], "collectible set"):
                    $itemArray[type] = "action figure";
                    break;

                case stristr($itemArray['OEMscale'], "bust"):
                    $itemArray['type'] = "bust";
                    break;

                case stristr($itemArray['OEMscale'], "premium format"):
                    $itemArray['scale'] = "1/4";
                    $itemArray['type'] = "statue";
                    break;

                case stristr($itemArray['OEMscale'], "life-size"):
                    $itemArray['scale'] = "1/1";
                    $itemArray['type'] = "statue";
                    break;

                case stristr($itemArray['OEMscale'], "legendary scale"):
                case stristr($itemArray['OEMscale'], "statue"):
                    $itemArray['type'] = "statue";
                    break;

                case stristr($itemArray['OEMscale'], "vinyl"):
                    $itemArray['type'] = "vinyl figure";
                    break;

                case stristr($itemArray['OEMscale'], "diorama"):
                    $itemArray['type'] = "diorama";
                    break;

                case stristr($itemArray['OEMscale'], "prop replica"):
                    $itemArray['scale'] = "1/1";
                    $itemArray['type'] = "prop replica";
                    break;

                case stristr($itemArray['OEMscale'], "replica"):
                    $itemArray['type'] = "replica";
                    break;

                case stristr($itemArray['OEMscale'], "apparel"):
                    $itemArray['scale'] = "1/1";
                    $itemArray['type'] = "apparel";
                    break;

                case stristr($itemArray['OEMscale'], "model kit"):
                    $itemArray['type'] = "model kit";
                    break;

                case stristr($itemArray['OEMscale'], "art print"):
                    $itemArray['type'] = "print";
                    break;

                case stristr($itemArray['OEMscale'], "book"):
                    $itemArray['scale'] = "1/1";
                    $itemArray['type'] = "book";
                    break;
            }
            
            // EXCLUSIVE
            if ($body->find("a[class=label-exclusive]", 0)->innertext) {
                $itemArray['exclusive'] = true;
            }
            
            return $itemArray;
        } 
        else {
            
            // end if the URL was read
            
            return 0;
        }
    }
} ?>