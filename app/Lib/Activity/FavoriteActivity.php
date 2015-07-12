<?php
App::uses('BaseActivity', 'Lib/Activity');
class FavoriteActivity extends BaseActivity {
    
    private $user;
    
    private $collectible;
    
    private $favorite;
    
    private $action;
    /**
     * Type will be the action, add or remove
     */
    public function __construct($action, $data) {
        $this->action = $action;
        $this->user = $data['user']['User'];
        
        if (isset($data['collectible'])) {
            $this->type = 'collectible';
            $this->collectible = $data['collectible'];
        } 
        else if (isset($data['userFavorite'])) {
            $this->type = 'user';
            $this->userFavorite = $data['userFavorite'];
        }
        
        parent::__construct();
    }
    
    public function buildActivityJSON() {
        $retVal = array();
        $retVal['published'] = date('Y-m-d H:i:s');
        // build the actor
        $actorJSON = $this->buildActor('user', $this->user);
        $retVal = array_merge($retVal, $actorJSON);
        // build the object we are acting on, in this case it is a comment
        // This should handle customs and originals by passing the $collectible data to the object
        // then when we go to render we can determine if it is a custom or original
        
        if ($this->type === 'collectible') {
            $objectJSON = $this->buildObject($this->collectible['Collectible']['id'], '/collectibles/view/' . $this->collectible['Collectible']['id'], 'collectible', $this->collectible);
            $retVal = array_merge($retVal, $objectJSON);
        } else if($this->type === 'user'){
            $objectJSON = $this->buildObject($this->userFavorite['User']['id'], '/stash/' . $this->userFavorite['User']['username'], 'user', $this->userFavorite);
            $retVal = array_merge($retVal, $objectJSON);        	
        }
        
        $stashDisplayname = '';
        
        $stashDisplayname = 'Favorites';
        $stashUrl = '/profile/' . $this->user['username'] . '/favorites';
        // Now add the target
        $targetJSON = $this->buildTarget($this->user['username'], $stashUrl, 'favorite', $stashDisplayname);
        $retVal = array_merge($retVal, $targetJSON);
        
        $verbJSON = $this->buildVerb($this->action);
        $retVal = array_merge($retVal, $verbJSON);
        
        return $retVal;
    }
}
?>