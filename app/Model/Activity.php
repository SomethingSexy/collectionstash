<?php
class Activity extends AppModel
{
    public $name = 'Activity';
    public $actsAs = array('Containable');
    public $belongsTo = array('User', 'ActivityType');
    /**
     * Right now, I am doing all activity processing server
     * side, so we are decoding here...if this becomes a performance
     * issue we will offload this to the client side
     */
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if ($primary && isset($val['Activity'])) {
                $data = json_decode($results[$key]['Activity']['data']);
                
                if ($data->actor->objectType === 'user') {
                    $data->actor->url = '/stash/' . $data->actor->displayName;
                }
                // edit doesen't have much so process this one separately
                if ($val['Activity']['activity_type_id'] === '12') {
                    $data->verb_displayName = __('editied');
                    $data->isObject = false;
                    if ($data->target->displayName === null) {
                        $data->target->displayName = __('Collectible');
                    }
                } else {
                    // process some verb display names
                    if ($val['Activity']['activity_type_id'] === '7') {
                        $data->verb_displayName = __('submitted');
                    } else {
                        if ($data->verb === 'add') {
                            $data->verb_displayName = __('added');
                        } else if ($data->verb === 'submit') {
                            $data->verb_displayName = __('submitted');
                        } else if ($data->verb === 'approve') {
                            $data->verb_displayName = __('approved');
                        } else if ($data->verb === 'remove') {
                            $data->verb_displayName = __('removed');
                        } else {
                            $data->verb_displayName = $data->verb;
                        }
                    }
                    // process object
                    // everyone except type 12 has an object (it should anyway)
                    $data->isObject = true;
                    // TODO: update activity code so it builds the object_displayName, this is a bit ridiculous
                    if ($data->object->objectType === 'photo') {
                        //<!-- need to handle old format, that does not contain the name -->
                        if ($val['Activity']['activity_type_id'] === '12') {
                            if (strrpos($data->object->url, '.') !== false) {
                                $data->object->url = $data->object->url . $data->object->data->name;
                            }
                        }
                        $data->object->object_displayName = $data->object->objectType;
                    } else if ($data->object->objectType === 'collectible') {
                        // I think really old activities this was missing
                        if ($data->object->data) {
                            $data->object->object_displayName = $data->object->data->Collectible->displayTitle;
                        } else {
                            $data->object->object_displayName = 'collectible';
                        }
                    } else if ($data->object->objectType === 'attribute') {
                        $data->object->object_displayName = $data->object->data->Attribute->name;
                    } else if ($data->object->objectType === 'tag') {
                        $data->object->object_displayName = $data->object->data->Tag->tag;
                        $data->object->url = '/collectibles/search/?t=' . $data->object->data->Tag->id;
                    } else if ($data->object->objectType === 'artist') {
                        $data->object->url = '/artist/' . $data->object->data->Artist->id . '/' . $data->object->data->Artist->slug;
                        $data->object->object_displayName = $data->object->data->Artist->name;
                    } else if ($data->object->objectType === 'listing') {
                        $data->object->object_displayName = 'listing';
                        $data->object->url = $data->object->data->Listing->url;
                    } else {
                        $data->object->object_displayName = $data->object->objectType;
                    }
                }
                
                if (isset($data->target) && !empty($data->target)) {
                    $data->isTarget = true;
                    
                    if ($data->verb === 'approve') {
                        $data->pre_target = __('submitted by');
                    } else if ($data->verb === 'remove') {
                        $data->pre_target = __('from');
                    } else {
                        $data->pre_target = __('to');
                    }
                    
                    if (empty($data->target->displayName)) {
                        if ($data->target->objectType === 'collectible') {
                            $data->target->displayName = 'Collectible';
                        } else if ($data->target->objectType === 'attribute') {
                            $data->target->displayName = 'Part';
                        }
                    }
                } else {
                    $data->isTarget = false;
                }
                
                $results[$key]['Activity']['data'] = $data;
            }
        }
        return $results;
    }
}
?>
