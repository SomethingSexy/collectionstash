<?php
App::uses('CakeEventListener', 'Event');
App::uses('CollectibleView', 'Model');

class TrackingEventListener implements CakeEventListener
{
    
    public function implementedEvents() {
        return array('Controller.Track.view' => 'trackView');
    }

    public function trackView($event) {
        $id = $event->data['id'];
        $type = $event->data['type'];
        $ip = $event->data['ip'];
        $user_id = $event->data['user_id'];
        
        $trackingData = array();
        
        $trackingData['CollectibleView'] = array();
        $trackingData['CollectibleView']['collectible_id'] = $id;
        $trackingData['CollectibleView']['ip'] = $ip;
        $trackingData['CollectibleView']['user_id'] = $user_id;
        
        $viewModel = new CollectibleView();
        $viewModel->create();
        $viewModel->saveAll($trackingData);
    }
}
?>