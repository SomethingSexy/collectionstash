<?php
class CollectibleView extends AppModel
{
    public $name = 'CollectibleView';
    public $actsAs = array('Containable');
    public function beforeSave($options = array()) {
        if (isset($this->data['CollectibleView']['ip'])) {
            $this->data['CollectibleView']['ip'] = inet_pton($this->data['CollectibleView']['ip']);
        }
        return true;
    }
}
?>