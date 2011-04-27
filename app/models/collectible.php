<?php
  class Collectible extends AppModel
  {
    var $name = 'Collectible';
    var $belongsTo = array (
        'Manufacture' => array(
            'className' => 'Manufacture','foreignKey'=>'manufacture_id'), 
        'Collectibletype' => array(
            'className' => 'Collectibletype','foreignKey'=>'collectibletype_id'), 
        'License' => array(
            'className' => 'License','foreignKey'=>'license_id'),
        'Series' => array(
             'className' => 'Series','foreignKey'=>'series_id'),
        'Approval'
    );    

    var $hasMany = array('CollectiblesUser', 'Upload', 'AttributesCollectible');    

    var $actsAs = array('ExtendAssociations','Containable');
    
    var $validate = array (
      'name' =>  array(
           'rule' => '/^[\\w\\s-]+$/' ,
           'required' => true,
           'message' => 'Alphanumeric only'
       ),
      'manufacture_id' => array(
           'rule' => array('validateManufactureId'),
           'required' => true,
           'message' => 'Must be a valid manufacture.'
       ),
      'collectibletype_id' => array(
           'rule' => array('validateCollectibleType'),
           'required' => true,
           'message' => 'Must be a valid type.'
       ),
      'description' => array(
           'minLength' => array(
              'rule' => 'notEmpty',
              'message' => 'Description is required.'
            ),
            'maxLength' => array(
              'rule' => array('maxLength', 1000),
              'message' => 'Invalid length.'
            )
       ),
      'msrp' => array(
           'rule' => array('money', 'left'),
           'required' => true,
           'message' => 'Please supply a valid monetary amount.'
       ),
      'edition_size' => array(
           'rule' => array('validateEditionSize'),
           'message' => 'Must be numeric.'
       ), 
      'upc' => array(
           'rule' => 'numeric',
           'required' => true,
           'message' => 'Must be numeric.'
       ),
       'product_length' => array (
            //This should be decmial or blank
           'rule' => '/^(?:\d{1,2}(?:\.\d{0,6})?)?$/',
           'required' => true,
           'message' => 'Must be a valid height.'       
       ),
       'product_width' => array (
           'validValues'=> array (
              //This should be decmial or blank
             'rule' => '/^(?:\d{1,2}(?:\.\d{0,6})?)?$/',
             'message' => 'Must be a valid width.'             
           ),
           'isRequired'=> array (
              'rule' => array('validateProductWidthDepthId'),
              'message' => 'Width is required.'           
           )
     
       ),
       'product_depth' => array (
           'validValues'=> array (
              //This should be decmial or blank
             'rule' => '/^(?:\d{1,2}(?:\.\d{0,6})?)?$/',
             'message' => 'Must be a valid depth.'             
           ),
           'isRequired'=> array (
              'rule' => array('validateProductWidthDepthId'),
              'message' => 'Depth is required.'           
           )
       ),
       'url' => array (
	   		'rule' => 'url',
	   		'required' => true,
	   		'message' => 'Must be a valid url.' 
	   )             
    );
    
    function beforeSave() 
    {
      //Update Edition Size stuff
      $editionSize = $this->data['Collectible']['edition_size'];
      //TBD = -1
      if ( (strcasecmp($editionSize, "TBD") == 0))
      {
        $this->data['Collectible']['edition_size'] = -1;
      }
      //None = -2
      else if (strcasecmp($editionSize, "None") == 0)  
      {
        $this->data['Collectible']['edition_size'] = -2;
      } 
      //If it is unknown = -3
      else if (trim($editionSize) == '')
      {
        $this->data['Collectible']['edition_size'] = -3;
      }
      
      return true;
    } 
    
    function doAfterFind($results) 
    {
    	
        if(isset($results['edition_size']))
         {
          $showEditionSize = TRUE;
          if ($results['edition_size'] == -1) 
          {
            $results['edition_size'] = "TBD";
            $showEditionSize = FALSE;
          }
          else if ($results['edition_size'] == -2)
          {
            $results['edition_size'] = "None";
            $showEditionSize =  FALSE;
          }
          else if ($results['edition_size'] == -3)
          {
            $results['edition_size'] = "Unknown";
            $showEditionSize = FALSE;
          }
			//debug($showEditionSize);
          	$results['showUserEditionSize'] = $showEditionSize;
        }
   
      debug($results);
      return $results;
    }
    
    function validateProductWidthDepthId($check)
    {
      $collectibleTypeId = $this->data['Collectible']['collectibletype_id'];
      
      if($collectibleTypeId != 1 && empty($check['collectibletype_id']))
      {
        return false;
      }
      else
      {
        return true;
      }
    }
    
    
    function validateEditionSize($check)
    {
        $isValid = false;
        $isInt = false;
        $editionSize = trim($check['edition_size']);
      
        //If it is unknown leave empty, which will eventually be a zero.
        if($editionSize == '')
        {
            debug($test='empty');
          return true;  
        }
        
        // First check if it's a numeric value as either a string or number
        if(is_numeric($editionSize) === TRUE)
        {
          debug($test='isnumeric');
            // It's a number, but it has to be an integer
            if((int)$editionSize == $editionSize)
            {
                debug($test='isint');
                if($editionSize > 0)
                {
                  debug($test='isgreaterthanzero');
                  return TRUE;
                }
               // return $isInt;
            // It's a number, but not an integer, so we fail
            } 
        // Not a number
        }
        else if ( (strcasecmp($editionSize, "TBD") == 0) || (strcasecmp($editionSize, "None") == 0) )
        {
          debug($test='istbdnone');
           return TRUE;  
        }

        return false;
    }
    
    function validateManufactureId($check)
    {
        $result = $this->Manufacture->find('count', array('id'=> $check));   
        return $result > 0;    
    }
    
    function validateCollectibleType($check)
    {
        $result = $this->Collectibletype->find('count', array('id'=> $check)); 
        return $result > 0;       
    }
    
    public function getCollectibleNameById($collectibleId)
    {
      //$this->Behaviors->attach('Containable');
      $result = $this->find("first",array(
        "conditions" => array("Collectible.id"=>$collectibleId),
      ));
      
      return $result['Collectible']['name'];
    }
    
    public function getAllCollectibles()
    {
        return $this->find('all');
    }

    public function getPendingCollectibles()
    {
      $collectible = $this->find("all", array(
        'conditions' => array('Approval.state'=> 1)
      ));
      
      return $collectible;
    }

    public function getNumberOfPendingCollectibles()
    {
      $count = $this->find("count", array(
        'conditions' => array('Approval.state'=> 1)
      ));
      
      return $count;
    }
    
    public function getPendingCollectiblesByUserId($userId)
    {
      $count = $this->find("count",array(
            'conditions'=>array('Approval.user_id'=>$userId, 'Approval.state'=> 1)
        )); 
      return $count;     
    }   
    
    public function getNumberofCollectiblesInStash($collectibleId)
    {
      $count = $this->CollectiblesUser->find("count", array(
         'conditions'=>array('CollectiblesUser.collectible_id'=>$collectibleId)
      ));
      $this->CollectiblesUser->Behaviors->attach('Containable');
      //TODO finish this, we want to return all userids to output other users hwo have this
      $count2 = $this->CollectiblesUser->find("all", array(
         'conditions'=>array('CollectiblesUser.collectible_id'=>$collectibleId),
         'contain' =>array('Stash'=>array('fields'=>'user_id')),
         'group'=>array('stash_id'),

      ));
      debug($count2); 
    
      return $count;
    } 

  }
?>
