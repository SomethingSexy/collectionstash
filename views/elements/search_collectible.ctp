<?php echo $this->Html->script('search-collectible',array('inline'=>false)); ?>
<div class="component" id="search-component">
  <div class="inside">
    <div class="component-view">
      <?php echo $this->Form->create(false , array('url' => $searchUrl));?>
       <div class="component-search-input">
         <fieldset>
            <ul class="form-fields">
              <li><?php echo $this->Form->input('Search.search', array('label' => false));?></li>
            </ul>
          </fieldset>
        </div>
   		<div class="component-search-filters">
   			<div class="filter-title">
   				<a>Filters</a>
   			</div>
   			<div class="filters">
	   			<fieldset>
		  			<ul class="form-fields">
		              <?php 
		              $filters = $session->read('Collectibles.filters');
					  $userSearchFields = $session->read('Collectibles.userSearchFields');
					  
					  $manufactures = $session->read('Manufacture_Search.filter');
		              foreach ($manufactures as $manufacture):
						$checked = false;
						if($userSearchFields)
		                {
		                  if($userSearchFields['Manufacture']['Filter'][$manufacture['Manufacture']['id']] == 1)
		                  {
		                    $checked = true;
		                  }
		                }	  
		              ?>
		                <li><?php echo $this->Form->input('Search.Manufacture.Filter.'.$manufacture['Manufacture']['id'], array('checked'=>$checked,'label' => $manufacture['Manufacture']['title'],'type' => 'checkbox'));?></li>  
		              <?php endforeach; ?>
		            </ul>
		            <ul class="form-fields">
		              <?php 
		              $collectibleTypes = $session->read('CollectibleType_Search.filter');
		              
		              
		              foreach ($collectibleTypes as $collectibleType):
		                $checked = false;
		              
		                if($userSearchFields)
		                {
		                  if($userSearchFields['CollectibleType']['Filter'][$collectibleType['Collectibletype']['id']] == 1)
		                  {
		                    $checked = true;
		                  }
		                }
		              ?>
		                <li><?php echo $this->Form->input('Search.CollectibleType.Filter.'.$collectibleType['Collectibletype']['id'], array('checked'=>$checked,'label' => $collectibleType['Collectibletype']['name'],'type' => 'checkbox'));?></li>  
		              <?php endforeach; ?>
		            </ul>        	
	        	</fieldset>
        	</div>       	
       </div>     
       <?php echo $this->Form->end(__('Search', true));?>      
    </div>
  </div>
</div>

          