<?php
class ActivitiesController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');
    
    public function index() {
        //Make sure the user is logged in
        $this->paginate = array('paramType' => 'querystring', 'limit' => 10, 'order' => array('Activity.created' => 'desc'), 'contain' => array('Activity', 'ActivityType', 'User' => array('fields' => array('id', 'username'))));
        $activities = $this->paginate('Activity');
        $this->set('activities', Set::extract('/Activity/.', $activities));
    }
}
?>
