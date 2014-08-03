<?php
class UserPointYearFact extends AppModel
{
    var $name = 'UserPointYearFact';
    var $actsAs = array('Containable');
    var $belongsTo = array('User');
    
    public function getYearlyLeaders() {
        $year = date("Y");
        return $this->getLeadersByYear($year);
    }
    
    public function getLeadersByYear($year) {
        $retVal = array();
        // Here we are handling making sure that the admin account is not including in this list anymore...need a better long term solution
        $retVal = $this->find('all', array('contain' => array('User' => array('fields' => array('username', 'id'))), 'limit' => 5, 'order' => array('UserPointYearFact.points' => 'desc'), 'conditions' => array('UserPointYearFact.year' => $year, 'NOT' => array('UserPointYearFact.user_id' => array('1')))));
        
        return $retVal;
    }
}
?>
