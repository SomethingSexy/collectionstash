<?php
App::uses('Sanitize', 'Utility');
class HomeController extends AppController
{
    
    public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
    public function beforeFilter() {
        parent::beforeFilter();
    }
    /**
     * This is going to do nothing for now.  The page has static text, unless the user is logged in then
     * they will see the catalog page.
     */
    public function index() {
        if ($this->isLoggedIn()) {
            $this->redirect(array('controller' => 'user', 'action' => 'home'));
        } else {
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
            
            $this->layout = 'require';
        }
    }
}
?>
