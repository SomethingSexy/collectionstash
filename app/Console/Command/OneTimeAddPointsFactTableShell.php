<?php

/**
 * This will be a one time shell that will take the total points prior to storing
 * activities and add them to the user point fact table so that we have the base
 * line in there
 *
 */
class OneTimeAddPointsFactTableShell extends AppShell
{
    public $uses = array('User', 'UserPointFact');
    
    public function main() {
        
        // Just grab all of the users and I will do any manually processing, should be faster
        $users = $this->User->find("all", array('contain' => false));
        
        foreach ($users as $key => $user) {
            debug($user);
            $saveData = array();
            $saveData['UserPointFact'] = array();
            $saveData['UserPointFact']['month'] = 11;
            $saveData['UserPointFact']['year'] = 2012;
            $saveData['UserPointFact']['user_id'] = $user['User']['id'];
            if (is_null($user['User']['points']) || empty($user['User']['points'])) {
                $saveData['UserPointFact']['points'] = 0;
            } else {
                $saveData['UserPointFact']['points'] = $user['User']['points'];
            }
            
            $this->UserPointFact->create();
            if ($this->UserPointFact->save($saveData)) {
            }
        }
    }
}
?>