<?php
class Tag extends AppModel {
    var $name = 'Tag';
    var $hasMany = array('CollectiblesTag');
    var $actsAs = array('Containable');

    var $validate = array('tag' => array('rule' => '/^[\\w\\s-.]+$/', 'required' => true, 'message' => 'Invalid characters'));
    /**
     * Might make more sense in the future to actually add tags to the table when they actually committ the collectible, but works for now.
     */
    public function processAddTags($tags) {
        $processedTags = array();
        //This is here to make sure we do not add the same one twice
        $added = array();
        foreach ($tags as $key => $value) {
            //If it has alrer
            if (!in_array($value['tag'], $added)) {
                array_push($added, $value['tag']);
                if (!empty($value['tag'])) {
                    $tagResult = $this -> find("first", array('contain' => false, 'conditions' => array('Tag.tag' => strtolower($value['tag']))));
                    debug($tagResult);
                    if (!empty($tagResult)) {
                        $collectibleTag = array();
                        $collectibleTag['tag_id'] = $tagResult['Tag']['id'];
                        $collectibleTag['Tag'] = $tagResult['Tag'];
                        array_push($processedTags, $collectibleTag);
                    } else {
                        //For now just set the active to true, later we might want to turn this back to not auto activate.
                        $value['active'] = 1;
                        $value['tag'] = strtolower($value['tag']);
                        $this -> create();
                        if ($this -> save($value)) {
                            $tagId = $this -> id;
                            $addedTag = $this -> findById($tagId);
                            $collectibleTag = array();
                            $collectibleTag['tag_id'] = $addedTag['Tag']['id'];
                            $collectibleTag['Tag'] = $addedTag['Tag'];
                            array_push($processedTags, $collectibleTag);
                        }
                    }
                }
            }
        }
        debug($processedTags);

        return $processedTags;
    }
    
    /**
     * This processes a single tag.  TODO: This needs to get merged with the above code.
     */
    public function processTag($tag) {
        $processedTag = null;
        if (!empty($tag['tag'])) {
            $tagResult = $this -> find("first", array('contain' => false, 'conditions' => array('Tag.tag' => strtolower($tag['tag']))));
            if (!empty($tagResult)) {
                $processedTag = array();
                $processedTag['Tag'] = $tagResult['Tag'];
         
            } else {
                //For now just set the active to true, later we might want to turn this back to not auto activate.
                $tag['active'] = 1;
                $tag['tag'] = strtolower($value['tag']);
                $this -> create();
                if ($this -> save($value)) {
                    $tagId = $this -> id;
                    $addedTag = $this -> findById($tagId);
                    $collectibleTag = array();
                    $processedTag['Tag'] = $addedTag['Tag'];
                }
            }
        }

        return $processedTag;
    }
}
?>
