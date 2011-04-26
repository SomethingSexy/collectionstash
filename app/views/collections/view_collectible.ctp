<div class="component" id="collectible-detail">
  <div class="inside">
  	<div class="component-title">
      <h2><?php __('Your Collectible Details');?></h2>
    </div>
    <div class="component-view">
    	<div class="collectibles">
	    	<div class="collectible item">
	        	<div class="collectible image"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => '100')); ?>
	              <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0)); ?></div>
	            </div>
	          	<div class="collectible detail">
	          	   <dl>
	          	     <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?><?php if($collectible['Collectible']['exclusive']){ __(' - Exclusive'); } ?> </dd>
	          	     <?php
	          	       if ($collectible['Collectible']['variant'])
	                   {
	                     echo '<dt>';
	                     __('Variant:');
	                     echo '</dt><dd>';
	                     __('Yes');
	                     echo '</dd>';
	                   }
	                 ?>  	     
	          	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Collectible']['Manufacture']['url']; ?>"><?php echo $collectible['Collectible']['Manufacture']['title']; ?></a></dd>
	          	     <dt>Type: </dt><dd><?php echo $collectible['Collectible']['Collectibletype']['name']; ?></dd>

		    		<dt><?php __('License'); ?></dt>
		  			<dd><?php echo $collectible['Collectible']['License']['name']; ?></dd>

		  			<dt><?php __('Description'); ?></dt>
		  			<dd><?php echo $collectible['Collectible']['description']; ?></dd>
					<?php if(!empty($collectible['Collectible']['code'])){ ?>
				    	<dt><?php __('Product Id'); ?></dt>
				    	<dd><?php echo $collectible['Collectible']['code']; ?></dd> 
				   	<?php } ?>
				   	<dt><?php __('Original Retail Price'); ?></dt>
		  			<dd><?php echo $collectible['Collectible']['msrp']; ?></dd>
					<?php 
				  		$editionSize = $collectible['Collectible']['edition_size']; 
				 		if(!$collectible['Collectible']['showUserEditionSize'])
		      			{ ?>
		   
		    			<dt><?php __('Edition Size'); ?></dt>
		        		<dd><?php echo $collectible['Collectible']['edition_size']; ?></dd>
		                 
		    		<?php }  
		      			else
		      			{ ?>
		          		<dt><?php __('Edition Size'); ?></dt>
		          		<dd><?php echo $collectible['CollectiblesUser']['edition_size'] . __(' of ', true) . $collectible['Collectible']['edition_size']; ?></dd>     
		     		<?php }  ?>	 
		      		<dt><?php __('Dimensions'); ?></dt>
		  			<dd><?php echo $collectible['Collectible']['product_length']; ?> x <?php echo $collectible['Collectible']['product_width']; ?> x <?php echo $collectible['Collectible']['product_depth']; ?> </dd>              
	               </dl>
	            </div>  
	          
	      </div>  
	      <div class="collectible statistics"> 	
	    	<h3><?php __('Your Collectible Statistics');?></h3>
	    	<dl>
	        	<dt><?php __('Total owned: '); ?></dt><dd><?php echo $collectibleCount; ?></dd>
	          	     
	        </dl>
	       	<?php echo $html->link('Who has it?', array('controller' => 'collections', 'action' => 'who', $collectible['Collectible']['id'])); ?>	 	
			</div>
      </div> 
      
    </div>
  </div>
</div>

