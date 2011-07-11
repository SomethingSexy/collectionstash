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
        'Approval',
        'Scale',
		'Retailer');

	var $hasMany = array('CollectiblesUser', 'Upload', 'AttributesCollectible', 'CollectiblesTag');

	var $actsAs = array('Revision', 'ExtendAssociations','Containable');

	var $validate = array ('name' =>  array(
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
       'license_id' => array(
	   		'rule' => array('validateLicenseId'),
	   		'message' => 'Brand/License must be valid for Manufacture.'
	   ),
	   'series_id' => array(
	   		'rule' => array('validateSeriesId'),
	   		'message' => 'Must be a valid category.'
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
	        'numeric' => array(
	           'rule' => 'numeric',
	           'allowEmpty' => true,
	           'message' => 'Must be numeric.'
	        ),
	        'maxLength' => array(
	          'rule' => array('maxLength', 10),
	          'message' => 'Invalid length.'
	        )
       ),
      'code' => array(
	        'numeric' => array(
	           'rule' => 'alphanumeric',
	           'allowEmpty' => true,
	           'message' => 'Must be alphanumeric.'
	        ),
	        'maxLength' => array(
	          'rule' => array('maxLength', 50),
	          'message' => 'Invalid length.'
	        )
       ),
       'product_length' => array (
            //This should be decmial or blank
           'rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/',
           'allowEmpty' => true,
           'message' => 'Must be a valid height.'       
       ),
       'product_width' => array (
           'validValues'=> array (
              //This should be decmial or blank
             'rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/',
             'message' => 'Must be a valid width.'             
           ),
           // 'isRequired'=> array (
              // 'rule' => array('validateProductWidthDepthId'),
              // 'message' => 'Width is required.'           
           // )
     
       ),
       'product_depth' => array (
           'validValues'=> array (
              //This should be decmial or blank
             'rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/',
             'message' => 'Must be a valid depth.'             
           ),
           // 'isRequired'=> array (
              // 'rule' => array('validateProductWidthDepthId'),
              // 'message' => 'Depth is required.'           
           // )
       ),
       'url' => array (
	   		'rule' => 'url',
	   		'required' => true,
	   		'message' => 'Must be a valid url.' 
	   )             
    );
    
	function beforeSave() {
		//Update Edition Size stuff
      	$editionSize = $this->data['Collectible']['edition_size'];
		$limited = $this->data['Collectible']['edition_size'];

      	if(trim($editionSize) != '' && !$limited) {
      		$editionSize = '';	
      	}
		
		//For whatever reason, cakephp year the put another array under the field
		if(isset($this->data['Collectible']['release']['year'])) {
			$year = $this->data['Collectible']['release']['year'];
			$this->data['Collectible']['release'] = $year;
		}

		//Check to see if these are set, if they are not, default them to false
		if(!isset($this->data['Collectible']['exclusive'])) {
			$this->data['Collectible']['exclusive'] = 0;
		}
		if(!isset($this->data['Collectible']['variant'])) {
			$this->data['Collectible']['variant'] = 0;
		}	
		
		$this->data['Collectible']['msrp'] = str_replace('$','', $this->data['Collectible']['msrp']);
		$this->data['Collectible']['msrp'] = str_replace(',','', $this->data['Collectible']['msrp']);
      
      	return true;
    } 
    
    function doAfterFind($results) {
		$showEditionSize = false;	
		if(isset($results['edition_size'])) {
          	if (!empty($results['edition_size'])) {
            	$showEditionSize = true;
          	} 
        }
		$results['showUserEditionSize'] = $showEditionSize;
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
    
    
    function validateEditionSize($check) {
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
        // else if ( (strcasecmp($editionSize, "TBD") == 0) || (strcasecmp($editionSize, "None") == 0) )
        // {
          // debug($test='istbdnone');
           // return TRUE;  
        // }

        return false;
    }
    
    function validateManufactureId($check) {
        $result = $this->Manufacture->find('count', array('id'=> $check['manufacture_id']));   
        return $result > 0;    
    }
	
    function validateLicenseId($check) {
        $result = $this->Manufacture -> LicensesManufacture-> find('first', 
        	array('conditions' => array(
        			'LicensesManufacture.manufacture_id' => $this->data['Collectible']['manufacture_id'],
					'LicensesManufacture.license_id' => $check['license_id']),
				'contain' => false));   
        debug($result);
		if($result) {
			return true;
			
		} else {
			return false;
		}
    }	
    
    function validateCollectibleType($check) {
 		$result = $this -> Manufacture -> CollectibletypesManufacture-> find('first', 
        	array('conditions' => array(
        			'CollectibletypesManufacture.manufacture_id' => $this->data['Collectible']['manufacture_id'],
					'CollectibletypesManufacture.collectibletype_id' => $check['collectibletype_id']),
				'contain' => false));   
        debug($result);
		if($result) {
			return true;
			
		} else {
			return false;
		}        	
    }

	/*
	 * TODO This will have to get updated when we allow more than one series 
	 */
    function validateSeriesId($check) {
    	debug($check);
    	//Is not required but if it is entered make sure it is valid
    	if(isset($check['series_id']) && !empty($check['series_id'])) {
	 		$result = $this->Manufacture -> LicensesManufacture-> find('first', 
	        	array('conditions' => array(
	        			'LicensesManufacture.manufacture_id' => $this->data['Collectible']['manufacture_id'],
						'LicensesManufacture.license_id' => $this->data['Collectible']['license_id']),
					'contain' => array('LicensesManufacturesSeries' => array('conditions' => array('series_id'=>$check['series_id'])))));    			
				
	        debug($result);
			if(!empty($result['LicensesManufacturesSeries'])) {
				return true;
			} else {
				return false;
			}       			
    	} else {
    		return true;
			
    	}
     	
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
         'group'=>array('stash_id')

	));
	debug($count2);

	return $count;
	}

	/**
	 * This method will return a list of collectible variants by
	 * the given id.
	 */
	public function getCollectibleVariants($collectibleId) {
		$collectibles = $this -> find('all', array(
			'conditions'=>array(
				'Collectible.variant_collectible_id' => $collectibleId	
			)));
			
		return $collectibles;
	}

	}?>
