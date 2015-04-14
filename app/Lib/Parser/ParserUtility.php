<?php
class ParserUtility {
    static public function capture_url($url) {
        // this function requires curl and php simple dom functions
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/600.5.6 (KHTML, like Gecko) Version/8.0.5 Safari/600.5.6');
        $chtml = curl_exec($curl);
        curl_close($curl);
        
        return str_get_html($chtml);
    }
    
    static public function htmlentities2utf8($string) {
        $string = html_entity_decode($string, ENT_QUOTES, $encoding = 'UTF-8');
        $string = str_replace("\xC2\xA0", ' ', $string);
        //convert non-breaking spaces
        return ($string);
    }
    // return the string between the two passed markers, or first marker and end of string
    static public function get_HTML_SubString($source, $begin_marker, $end_marker = "") {
        
        if (strlen($begin_marker) <= strlen($source)) {
            // make sure the marker text fits within the source, otherwise don't even try
            $start = stripos($source, $begin_marker);
            
            if ($start === false) return;
            else $start = $start + strlen($begin_marker);
            // set the start position
            
            $end = stripos($source, $end_marker, $start);
            // set the end position
            
            if (empty($end)) $end = strlen($source);
            // set new end position if unmatched or no end text passed
            
            if ($start >= strlen($begin_marker)) {
                return substr($source, $start, $end - $start);
            } 
            else {
                return;
            }
        } 
        else {
            return;
        }
    }
    
    static public function feet2inches($value) {
        
        if (strstr($value, "'")) {
            $value = str_replace("'", "", $value);
            $value = $value * 12;
        }
        return $value;
        // return either the converted number or the original - this is not general purpose and has no error codes
        
        
    }
    //Pull product details from SSC product page REMOVING ALL HTML TAGS
    static public function sscProductDetails($source, $detail) {
        
        $start = stripos($source, $detail . "</dt>");
        $start = $start + strlen($detail . "</dt>");
        $end = stripos($source, "</dd>", $start);
        
        if ($start > strlen($detail . "</dt>")) {
            return trim(ParserUtility::htmlentities2utf8(strip_tags(substr($source, $start, $end - $start))));
        } 
        else {
            return;
        }
    }
    //Pull product details from SSC product page KEEPING HTML TAGS
    static public function sscProductDetailsHTML($source, $detail) {
        
        $start = stripos($source, $detail . "</dt>");
        $start = $start + strlen($detail . "</dt>");
        $end = stripos($source, "</dd>", $start);
        
        if ($start > strlen($detail . "</dt>")) {
            return trim(ParserUtility::htmlentities2utf8(substr($source, $start, $end - $start)));
        } 
        else {
            return;
        }
    }
    // check if a string starts with a substring - case sensitive
    static public function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    // check if a string ends with a substring - case sensitive
    static public function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        
        return (substr($haystack, -$length) === $needle);
    }
    // Check whether a string contains a particular phrase (one or more words)
    // case insensitive, a phrase is not a substring - 4 possibilities for true below
    static public function containsPhrase($haystack, $needle) {
        
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        
        if (strlen($needle) > strlen($haystack)) {
            return false;
        } 
        else if ($needle == $haystack) { // exact match
            return true;
        } 
        else if (ParserUtility::startswith($haystack, $needle . " ")) { // starts with match followed by a space
            return true;
        } 
        else if (ParserUtility::endswith($haystack, " " . $needle)) { // ends with match preceded by a space
            return true;
        } 
        else if (stristr($haystack, " " . $needle . " ")) { // contains match padded with a space on both ends
            return true;
        } 
        else {
            return false;
        }
    }
    
    static public function scale2Words($scale) {
        $words = array();
        
        switch ($scale) {
            case "1/12":
            case "1:12":
                $words[] = "twelfth scale";
            break;
            case "1/10":
            case "1:10":
                $words[] = "tenth scale";
            break;
            case "1/9":
            case "1:9":
                $words[] = "ninth scale";
            break;
            case "1/8":
            case "1:8":
                $words[] = "eighth scale";
            break;
            case "1/7":
            case "1:7":
                $words[] = "seventh scale";
            break;
            case "1/6":
            case "1:6":
                $words[] = "sixth scale";
            break;
            case "1/5":
            case "1:5":
                $words[] = "fifth scale";
            break;
            case "1/4":
            case "1:4":
                $words[] = "quarter scale";
                $words[] = "fourth scale";
            break;
            case "1/3":
            case "1:3":
                $words[] = "third scale";
            break;
            case "1/2":
            case "1:2":
                $words[] = "half scale";
                $words[] = "second scale";
            break;
            case "1/1":
            case "1:1":
                $words[] = "life size";
                $words[] = "full scale";
            break;
        }
        
        return $words;
    }
    // converts a string into title case - this is not HTML aware and will capitalize tag contents
    // input should be plain text without tags
    //
    // -BF- I didn't write this function, just mofified it.
    static public function titleCase($string) {
        // convert everything to lowercase first
        $string = strtolower($string);
        // list of words we don't want to capitalize
        $smallWords = array('of', 'a', 'as', 'the', 'and', 'an', 'or', 'nor', 'but', 'is', 'if', 'then', 'else', 'when', 'at', 'from', 'by', 'on', 'off', 'for', 'in', 'out', 'over', 'to', 'into', 'with', 'cm', 'vs');
        // special words that should be written as-is
        $specialWords = array('3A', 'ThreeA', '2000AD', 'EMGY', 'NYC', 'JDF', 'NOM', 'AP', 'TK', 'TQ', 'WWR', 'JC', 'A-Level', '\'s', 'UK','UKTK', 'USA', 'HK', 'II', 'III', 'IV', 'V', 'VI', 'VII', '3AGO');
        // punctuation used to determine that the following letter
        // should be capitalised
        $punctuation = array('.', '-', ':', '!', '\'', '-', '?');
        // replacements
        $replacements[] = array();
        // removed "'", "\'" escapes
        
        // replace any non-letters or numbers with spaces so we
        // know what the actual words are
        $cleanString = preg_replace("/[^\w]/", ' ', $string);
        // the original string split into an array of individual
        // characters so we can replace the modified characters
        $originalStringSplit = str_split($string);
        // split the string of letters and spaces only into an array
        $allWords = explode(' ', $cleanString);
        // go through each element in the array and check whether
        // the word appears in the short words list
        // if it is not, we need to capitalize the word
        
        foreach ($allWords as $key => $word) {
            if (!in_array($word, $smallWords)) {
                $allWords[$key] = ucfirst($word);
                // special case for Mc and Mac and Fitz and O' words
                $allWords[$key] = preg_replace('/(?: ^ | \\b )( O\' | Ma?c | Fitz)( [^\W\d_] )/xe', "'\$1' . strtoupper('\$2')", $allWords[$key]);
            }
        }
        // convert the array back to a string
        $allWords = implode(' ', $allWords);
        // the title-cased string split into characters so we can
        // replace them original characters with them
        
        $titleStringSplit = str_split($allWords);
        // check through each character and replace the one stored
        // in the original string if it is a letter
        
        foreach ($titleStringSplit as $key => $char) {
            if ($char != " ") {
                $originalStringSplit[$key] = $char;
            }
        }
        // join all the characters back into a string
        $titleString = implode('', $originalStringSplit);
        // make the first letter after certain punctuation capitalized,
        // regardless of the normal rules. I.e. "Shakespeare: The Bard"
        foreach ($punctuation as $char) {
            // match anything which starts with the punctuation type that
            // we are checking for until the first letter which follows it
            // and replace that with a capitalized version of the string
            // I.e.
            // : the => |: t| => : The
            // Twentieth-century => |-c| => Twentieth-Century
            $titleString = preg_replace("/(" . preg_quote($char) . "\s*[a-zA-Z])/ie", "strtoupper('\\1')", $titleString);
        }
        // capitalize the very first letter of the sentence, as it may
        // appear after punctuation which we would not normally
        // use to determine whether a word should be capitalised
        //  - 0-9 don't capitalize first character immediately after a number (like 6cm)
        $titleString = preg_replace("/(^[^a-zA-Z0-9]*[a-zA-Z])/ie", "strtoupper('\\1')", $titleString);
        // find and replace text
        foreach ($replacements as $replacement) {
            $find = $replacement[0];
            $replace = $replacement[1];
            $titleString = str_ireplace($find, $replace, $titleString);
        }
        // sort out any "special" words last so they are not
        // overwritten with our previous rules
        foreach ($specialWords as $specialWord) {
            // check for each special word, regardless of case and
            // replace it with the word stored in the array
            $titleString = preg_replace("/\b" . $specialWord . "\b/i", $specialWord, $titleString);
        }
        
        return $titleString;
    }
} ?>