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
                if ($val['Activity']['activity_type_id}'] === '12') {
                    $data->verb_displayName = __('editied');
                    if ($data->tagert->displayName === null) {
                        $data->tagert->displayName = __('Collectible');
                    }
                } else {
                }
                
                $results[$key]['Activity']['data'] = $data;
            }
        }
        return $results;
    }
}
?>
