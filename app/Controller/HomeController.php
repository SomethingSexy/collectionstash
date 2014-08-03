<?php
App::uses('Sanitize', 'Utility');
class HomeController extends AppController
{
    
    public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
    public function beforeFilter() {
        parent::beforeFilter();
    }
    
    public function index() {
        $this->loadModel('Collectible');
        // Now grab the pending collectible
        $pending = $this->Collectible->getPendingCollectibles(array('limit' => 4, 'order' => array('Collectible.created' => 'desc')));
        $totalPending = $this->Collectible->getNumberOfPendingCollectibles();
        
        $extractPending = Set::extract('/Collectible/.', $pending);
        
        foreach ($extractPending as $key => $value) {
            $extractPending[$key]['CollectiblesUpload'] = $pending[$key]['CollectiblesUpload'];
        }
        
        $this->set('pending', $extractPending);
        
        $this->set(compact('totalPending'));
        
        $totalNew = $this->Collectible->find('count', array('conditions' => array('Collectible.status_id' => 4), 'limit' => 4));
        $newCollectibles = $this->Collectible->find('all', array('conditions' => array('Collectible.status_id' => 4), 'order' => array('Collectible.modified' => 'desc'), 'contain' => array('User', 'Collectibletype', 'Manufacture', 'Status', 'CollectiblesUpload' => array('Upload')), 'limit' => 4));
        
        $extractNewCollectibles = Set::extract('/Collectible/.', $newCollectibles);
        
        foreach ($extractNewCollectibles as $key => $value) {
            $extractNewCollectibles[$key]['CollectiblesUpload'] = $newCollectibles[$key]['CollectiblesUpload'];
        }
        $this->set('newCollectibles', $extractNewCollectibles);
        $this->set(compact('totalNew'));
        
        $this->loadModel('Activity');
        $activity = $this->Activity->find('all', array('limit' => 10, 'order' => array('Activity.created' => 'desc')));
        $this->set('activity', Set::extract('/Activity/.', $activity));
        $totalActivity = $this->Activity->find('count');
        $this->set(compact('totalActivity'));
        
        $this->loadModel('UserPointFact');
        $this->loadModel('UserPointYearFact');
        
        $monthlyLeaders = $this->UserPointFact->getCurrentMonthlyLeaders();
        $extractMonthlyLeaders =  Set::extract('/UserPointFact/.', $monthlyLeaders);
        foreach ($extractMonthlyLeaders as $key => $value) {
            $extractMonthlyLeaders[$key]['User'] = $monthlyLeaders[$key]['User'];
        }        
        $this->set('monthlyLeaders', $extractMonthlyLeaders);
        
        $previousMonthlyLeaders = $this->UserPointFact->getPreviousMonthyLeaders();
        $extractPreviousMonthlyLeaders =  Set::extract('/UserPointFact/.', $previousMonthlyLeaders);
        foreach ($extractPreviousMonthlyLeaders as $key => $value) {
            $extractPreviousMonthlyLeaders[$key]['User'] = $previousMonthlyLeaders[$key]['User'];
        }        
        $this->set('previousMonthlyLeaders', $extractPreviousMonthlyLeaders);
        
        $yearlyLeaders = $this->UserPointYearFact->getYearlyLeaders();
        $extractYearlyLeaders =  Set::extract('/UserPointYearFact/.', $yearlyLeaders);
        foreach ($extractYearlyLeaders as $key => $value) {
            $extractYearlyLeaders[$key]['User'] = $yearlyLeaders[$key]['User'];
        }        
        $this->set('yearlyLeaders', $extractYearlyLeaders);
        
        $this->layout = 'require';
    }
}
?>
