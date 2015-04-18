<?php
require_once (dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'sunra' . DS . 'php-simple-html-dom-parser' . DS . 'Src' . DS . 'Sunra' . DS . 'PhpSimple' . DS . 'HtmlDomParser.php');
App::uses('Parsable', 'Lib/Parser');
App::uses('ParserUtility', 'Lib/Parser');
App::uses('CollectibleObj', 'Lib');
class Sideshow implements Parsable {
    
    public function __construct() {
    }
    
    public function parse($url) {
        // this is only available for NEW non-archived products - dont use it right now
        //$ecommerce_text = get_HTML_SubString ($html, "ecommerceDetailItem : {","}");
        //$ecommerce_text = trim(preg_replace("/\s+/", " ", $ecommerce_text));
        
        $itemArray = array();
        // check productScraper function for full list of key names
        
        if ($html = ParserUtility::capture_url($url)) {
            debug($html);
            //it seems all products have an id product on there, if that doesn't exist, fail fast
            if (!$html->find('div[id=product]')) {
                return false;
            }
            
            $collectible = new CollectibleObj();
            
            $head = $html->find("head", 0);
            $body = $html->find("body", 0);
            //URL
            $collectible->url = $head->find("meta[property='og:url']", 0)->getAttribute('content');
            //NAME
            $collectible->name = ParserUtility::htmlentities2utf8($body->find("h1", 0)->innertext);
            // GALLERY IMAGES
            $images = ($body->find("div[id=gallery]", 0)->find("a"));
            
            foreach ($images as $value) {
                if (substr($value->href, 0, 1) == "/") {
                    array_push($collectible->photos, "http://www.sideshowtoy.com" . $value->href);
                } 
                else {
                    array_push($collectible->photos, $value->href);
                }
            }
            //GIANT IMAGE
            $giantimage = ParserUtility::get_HTML_SubString($body, "<!-- Product Details -->", "<!-- Product Silo Image -->");
            
            if (!empty($giantimage)) {
                $giantimage = str_get_html($giantimage);
                $giantimage = $giantimage->find("img", 0)->src;
                
                if (stristr($giantimage, "sideshowtoy.com")) {
                    $giantimage = ParserUtility::get_HTML_SubString($giantimage, ".com/");
                    $giantimage = "http://www.sideshowtoy.com/" . $giantimage;
                } 
                else {
                    $giantimage = "http://www.sideshowtoy.com" . $giantimage;
                }
            }
            
            if ($giantimage) {
                array_push($collectible->photos, $giantimage);
            }
            //FEATURE IMAGE
            $featureimage = $head->find("meta[property='og:image']", 0)->getAttribute('content');
            
            if ($featureimage) {
                array_push($collectible->photos, $featureimage);
            }
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
            
            //check to see if this is a sale item
            $cost = ParserUtility::get_HTML_SubString($body, 'MSRP', '</strike>');
            if ($cost) {
                $cost = strip_tags(ParserUtility::htmlentities2utf8($cost));
                // clean the text so we're left with a float
                $cost = preg_replace("/[^0-9.]/", "", $cost);
            } 
            else {
                $cost = ParserUtility::get_HTML_SubString($body, 'price: "$', '"');
            }
            $collectible->cost = floatval($cost);
            // UPC - sanitize a bit - only numbers, only up to 13 digits
            $collectible->upc = substr(preg_replace("/\D/", "", ParserUtility::sscProductDetails($body, 'upc')), 0, 12);
            // DIMENSIONS - Sideshow are very consistent with the format
            // there's some repetition in the code, but not worth replacing with a function
            $size = ParserUtility::sscProductDetailsHTML($body, 'size');
            // split into multiple lines if there are dimension sets for more than one part of this product (example: product + stand)
            $size = explode("<hr/>", $size);
            // scoop up all other dimension sets as plain text
            for ($i = 1, $len = count($size);$i < $len;++$i) {
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
            if ($weight !== 0.0) {
                $collectible->weight = $weight;
            }
            // MANUFACTURER
            $collectible->manufacturer = ParserUtility::sscProductDetails($body, 'manufacturer');
            // SERIES - header3 content - sometimes used for license name, otherwise used for sub-license/series
            $collectible->series = trim(ParserUtility::htmlentities2utf8($body->find("h3", 0)->innertext));
            $collectible->series = trim(str_ireplace("figure set", "", $collectible->series));
            if (stristr($collectible->series, "version")) {
                $collectible->series = "";
            }
            // LICENSE - pre-populate license with series content just in case the license is blank
            $collectible->brand = $collectible->series;
            
            $brand = ParserUtility::sscProductDetails($body, 'license');
            if ($brand) {
                $collectible->brand = $brand;
                // Hack/Fix for ThreeA when "Ashley Wood" is listed as License
                // Grab License from product name, add "Ashley Wood" as artist
                if ($collectible->brand == "Ashley Wood") {
                    
                    $addArtist = $collectible->brand; // hold artist name to add to artists list later
                    $collectible->brand = "";
                    
                    if (!empty($itemArray['series'])) {
                        $collectible->brand = $itemArray['series'];
                        $collectible->series = "";
                    } 
                    elseif (stristr($collectible->name, "tomorrow") || stristr($collectible->description, "tomorrow kings")) {
                        $collectible->brand = "Popbot";
                        $collectible->series = "Tomorrow Kings";
                        $collectible->name = trim(str_ireplace("tomorrow kings", "", $collectible->name));
                        $collectible->name = trim(str_ireplace("tomorrow queens", "", $collectible->name));
                    } 
                    elseif (stristr($collectible->name, "popbot") || stristr($itemArray['description'], "popbot")) {
                        $collectible->brand = "Popbot";
                    } 
                    elseif (stristr($collectible->name, "wwrp") || stristr($itemArray['description'], "wwrp")) {
                        $collectible->brand = "World War Robot";
                        $collectible->series = "World War Robot Portable";
                        $collectible->name = trim(str_ireplace("wwrp", "", $itemArray['name']));
                    } 
                    elseif (stristr($collectible->name, "wwr") || stristr($collectible->description, "wwr") || stristr($collectible->description, "world war robot")) {
                        $collectible->brand = "World War Robot";
                        $collectible->name = trim(str_ireplace("wwr", "", $collectible->name));
                    } 
                    elseif (stristr($collectible->name, "kartel") || stristr($collectible->description, "kartel")) {
                        $collectible->brand = "Adventure Kartel";
                        $collectible->name = trim(str_ireplace("adventure kartel", "", $itemArray['name']));
                    } 
                    elseif (stristr($collectible->name, "evenfall") || stristr($collectible->description, "evenfall")) {
                        $collectible->brand = "Evenfall";
                        $collectible->name = trim(str_ireplace("evenfall", "", $collectible->name));
                    }
                } // end if Hack/Fix Ashley Wood
                
                
            }
            // SKU
            if ($collectible->manufacturer === 'Sideshow Collectibles') {
                $collectible->productCode = ParserUtility::sscProductDetails($body, 'sku');
            }
            // EDITION
            $collectible->editionsize = ParserUtility::get_HTML_SubString($body, "<!-- .labels -->", "<!-- .col -->");
            if (stristr($collectible->editionsize, "limited")) {
                $collectible->limited = true;
                $collectible->editionsize = preg_replace("/\D/", "", $collectible->editionsize);
            } 
            else {
                $collectible->editionsize = null;
            }
            // ARTISTS
            $tempHTML = str_get_html(ParserUtility::get_HTML_SubString($body, "<!-- artists -->", "<!-- authors -->"));
            $artists = array();
            // artists as HTML list items
            if ($artists = $tempHTML->find("li")) {
                
                foreach ($artists as & $value) {
                    $value = ParserUtility::htmlentities2utf8($value);
                    $value = trim(preg_replace("/[\[{\(].*[\]}\)]/U", "", strip_tags($value)));
                    // remove HTML and bracketed content from artist names
                    
                    if ($cleaned = ParserUtility::get_HTML_SubString($value, " by ")) {
                        // trim pre-text if it contains credits (head painted by artist name)
                        $value = $cleaned;
                    }
                }
                unset($value);
            } 
            else {
                $tempHTML = ParserUtility::htmlentities2utf8($tempHTML);
                $tempHTML = str_ireplace("artists", "", $tempHTML);
                $tempHTML = str_ireplace("artist", "", $tempHTML);
                $tempHTML = str_ireplace(":", "", $tempHTML);
                $tempHTML = preg_replace("/[\[{\(].*[\]}\)]/U", "", strip_tags($tempHTML));
                // remove bracketed content (and strip HTML tags)
                
                $artists = explode(",", trim($tempHTML));
                
                foreach ($artists as & $value) {
                    $value = trim($value);
                    if ($cleaned = ParserUtility::get_HTML_SubString($value, " by ")) { // trim pre-text if it contains credits (head painted by artist name)
                        $value = $cleaned;
                    }
                }
                unset($value);
            }
            // extra artist picked up from another field (license for Ashely Wood for instance)
            if (isset($addArtist)) {
                $artists[] = $addArtist;
            }
            
            $artists = array_unique($artists); // remove any duplicates
            $artists = array_filter($artists); // don't keep any blanks (unsets blank keys)
            $artists = array_values($artists); // re-index the array (compacts it)
            
            // we don't want this as an artist
            if (($key = array_search('The Sideshow Collectibles Design and Development Team', $artists)) !== false) {
                unset($artists[$key]);
            }
            
            $collectible->artists = $artists;
            // YEAR
            // pre-populate from copyright date if available
            $copyright = ParserUtility::get_HTML_SubString($body, "<!-- legal -->", "<!-- .details -->");
            $copyright = ParserUtility::htmlentities2utf8(strip_tags(preg_replace("/\s+/", " ", $copyright)));
            // grab the highest number year from the shipping date for pre-order products
            if ($shipyear = ParserUtility::sscProductDetails($body, 'ship')) {
                // grab all years from ship date (in case of range) and use the largest as the initial date of the product
                preg_match_all('/(\d{4})/', $shipyear, $allYears);
                
                $shipyear = "";
                foreach ($allYears[0] as $value) {
                    if ($value > $shipyear) $shipyear = $value;
                }
            }
            // grab all years from copyright and compare the largest against previous grabbed year numbers
            preg_match_all('/(\d{4})/', $copyright, $allYears);
            
            foreach ($allYears[0] as $value) {
                if (isset($orderyear) && $value > $orderyear) {
                    $orderyear = $value;
                }
            }
            // if all else fails, grab the year from the feature image folder path
            $imgURLparts = explode('/', $featureimage);
            
            $imgYear = $imgURLparts[count($imgURLparts) - 3];
            
            if (strlen($imgYear) == 4 && $imgYear < 2050 && $imgYear > 1990) $imgyear = $imgYear;
            // use the img path date as the order year if greater than existing order year (copyright)
            
            if (!isset($orderyear) || $orderyear < $imgyear) {
                $collectible->releaseYear = $imgyear;
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
                    $collectible->scale = "1:6";
                    $collectible->type = "action figure accessory";
                break;
                case stristr($OEMscale, "maquette"):
                    $collectible->type = "maquette";
                break;
                case stristr($OEMscale, "sixth scale"):
                    $collectible->scale = "1:6";
                    $collectible->type = "action figure";
                break;
                case stristr($OEMscale, "quarter scale"):
                    $collectible->scale = "1:4";
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
                    $collectible->scale = "1:4";
                    $collectible->type = "statue";
                break;
                case stristr($OEMscale, "life-size"):
                    $collectible->scale = "1:1";
                    $collectible->type = "statue";
                break;
                case stristr($OEMscale, "legendary scale"):
                case stristr($OEMscale, "statue"):
                    $collectible->scale = "1:2";
                    $collectible->type = "statue";
                break;
                case stristr($OEMscale, "vinyl"):
                    $collectible->type = "vinyl figure";
                break;
                case stristr($OEMscale, "diorama"):
                    $collectible->type = "diorama";
                break;
                case stristr($OEMscale, "prop replica"):
                    $collectible->scale = "1:1";
                    $collectible->type = "prop replica";
                break;
                case stristr($OEMscale, "replica"):
                    $collectible->type = "replica";
                break;
                case stristr($OEMscale, "apparel"):
                    $collectible->scale = "1:1";
                    $collectible->type = "apparel";
                break;
                case stristr($OEMscale, "model kit"):
                    $collectible->type = "model kit";
                break;
                case stristr($OEMscale, "art print"):
                    $collectible->type = "print";
                break;
                case stristr($OEMscale, "book"):
                    $collectible->scale = "1:1";
                    $collectible->type = "book";
                break;
            }
            // EXCLUSIVE
            if ($body->find("a[class=label-exclusive]", 0)) {
                $collectible->exclusive = true;
            }
            //KEYWORDS          // TODO might be a little overzealous to strip colons and slashes and dashes - could have scale info, like 1/2 scale or half-scale
            // or words like "x-men"
            
            $itemArray['keywords'] = strtolower(ParserUtility::htmlentities2utf8($head->find("meta[property='og:keywords']", 0)->getAttribute('content')));
            //      echo $itemArray[keywords]."<br/>"; // DEBUG
            
            // remove all keywords if they're generic Sideshow website keywords
            if (stristr($itemArray['keywords'], "movie television and proprietary collectible figures")) $itemArray['keywords'] = "";
            else if (stristr($itemArray['keywords'], "collectibles, collectible figures, movie collectibles, movie memorabilia, pop culture figures,")) $itemArray['keywords'] = "";
            
            if (!empty($itemArray['keywords'])) { // make and clean-up keywords
                
                // remove redundant keywords (other product fields) and some generic words
                $invalid_Words = array($collectible->brand, "stuff", "action figures", "action figure", "statues", "statue", "collectables", "collectibles", "collectible", "figures", "figure", "the ", "items", "item", "pieces", "piece");
                
                $invalid_Words = array_merge($invalid_Words, $collectible->artists);
                // temporary array with all words from product name and manufacturer to filter out of keywords
                $tempNameWords = $collectible->name . " " . $collectible->manufacturer;
                //          $tempNameWords = preg_replace('/[^[:alpha:],:\/\- ]/u',"",$tempNameWords );  // no longer needed, duplicated below
                
                $tempNameWords = explode(" ", $tempNameWords);
                // remove whole words in product name & manufacturer from keywords
                foreach ($tempNameWords as $value) {
                    $pattern = '/\b' . preg_quote($value) . '\b/ui';
                    $itemArray['keywords'] = preg_replace($pattern, "", $itemArray['keywords']);
                }
                
                $itemArray['keywords'] = preg_replace('/[^[:alnum:],:\/\- ]/u', "", $itemArray['keywords']); //
                
                $itemArray['keywords'] = str_ireplace($invalid_Words, "", $itemArray['keywords']);
                //          echo "<br/>".$itemArray[keywords]."<br/>"; // DEBUG
                
                // break keyword string into array of individual words/terms (on comma)
                $itemArray['keywords'] = explode(",", $itemArray['keywords']);
                foreach ($itemArray['keywords'] as & $value) {
                    $value = trim($value);
                }
                unset($value);
                
                $itemArray['keywords'] = array_values(array_unique($itemArray['keywords'])); // remove exact duplicates & re-index the result
                
                $singleInvalidWords = array("sideshow", "exclusive", "limited", "series", "version");
                // remove part of a keyword if it's already self-contained as its own keyword - example: "han solo" and "hoth han solo"
                foreach ($itemArray['keywords'] as $key => & $value) { // check each keyword to see if it exists within another
                    // example: "planet" within "big planet" but not "planetarium"
                    
                    // Capture scale keywords if we don't yet have a prodyct scale (into temp array)
                    if (empty($collectible->scale)) {
                        if (stristr($value, "scale")) {
                            $tempScale = $value;
                            $tempScale = str_ireplace("half", "1/2", $tempScale); // some substitutions
                            $tempScale = str_ireplace("quarter", "1/4", $tempScale);
                            $tempScale = str_ireplace("sixth", "1/6", $tempScale);
                            
                            $tempScale = preg_replace("/[^\d\":\/.]/", "", trim($tempScale)); // get rid of remaining non-scale text
                            $tempScale = str_replace(":", "/", $tempScale);
                            
                            $tempKeyScale[] = $tempScale;
                            unset($tempScale);
                        }
                    }
                    
                    if (strlen($value) < 3) { // remove 1 or 2 character keywords
                        $value = "";
                        continue;
                    }
                    // get rid of: sideshow, exlusive, limited, series
                    foreach ($singleInvalidWords as $singleWord) {
                        if ($value == $singleWord) { // remove site from keywords
                            $value = "";
                            break;
                        }
                    }
                    
                    if ($value == "") continue;
                    
                    for ($i = 0;$i < count($itemArray['keywords']);++$i) { // compare current keyword against all others
                        
                        if (($i !== $key) && (ParserUtility::containsPhrase($itemArray['keywords'][$i], $value))) { // don't compare against itself
                            
                            // remove the matching keyword from the larger keyword
                            $itemArray['keywords'][$i] = trim(str_ireplace($value, "", $itemArray['keywords'][$i]));
                            // replace multiple spaces with a single space
                            $itemArray['keywords'][$i] = preg_replace('!\s+!', ' ', $itemArray['keywords'][$i]);
                            // remove non-alphanumerics from start and end of string
                            $itemArray['keywords'][$i] = preg_replace('/(^[^[:alnum:]]+)|([^[:alnum:]]+\Z)/ui', '', $itemArray['keywords'][$i]);
                            
                            if (strlen($itemArray['keywords'][$i]) < 3) { // remove 1 or 2 character keywords
                                $itemArray['keywords'][$i] = "";
                            }
                        }
                    }
                }
                unset($value);
            } // end if make and clean-up keywords
            
            // Populate product scale if we pulled scale from the keywords
            if (!empty($tempKeyScale)) {
                $tempKeyScale = array_filter($tempKeyScale); // unsets empty values
                $tempKeyScale = array_values(array_unique($tempKeyScale)); // re-index the array (compacts it)
                
                $collectible->scale = $tempKeyScale[0]; // just grab the first derived scale as the scale value - as good as any
                
                
            }
            // add some derived keywords based on other fields (scale)
            $itemArray['keywords'] = array_merge($itemArray['keywords'], ParserUtility::scale2Words($collectible->scale));
            
            $itemArray['keywords'] = array_filter($itemArray['keywords']); // unsets empty values
            $itemArray['keywords'] = array_values(array_unique($itemArray['keywords'])); // re-index the array (compacts it)
            
            // END KEYWORDS
            
            return $collectible;
        } 
        else {
            // end if the URL was read
            
            return 0;
        }
    }
} ?>