<?php echo $this->Html->script('jquery.form',array('inline'=>false)); ?>
<?php echo $this->Html->script('stash',array('inline'=>false)); ?>
<?php echo $this->Html->script('user-home',array('inline'=>false)); ?>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Stash</a></li>
		<li><a href="#tabs-2">History</a></li>
		<li><a href="#tabs-3">Profile</a></li>
	</ul>
	<div id="tabs-1">
		<div id="my-stashes-component" class="component">
		  <div class="inside">
		    <div class="component-title">
		      <h2><?php __('My Stash');?></h2>
		    </div>
		    <?php echo $this->element('flash'); ?>
		    <div class="component-view">
		    	<div class="actions">
		    		<ul>
		    			<li><?php echo $html->link('View', array('controller' => 'stashs',$myCollectibles[0]['Stash']['id'])); ?></li>
		    			<li><?php echo $html->link('Add', array('controller' => 'collections','action'=>'addSearch', 'stashId' => $myCollectibles[0]['Stash']['id'],'initial'=>'yes')); ?></li>
		    			<li><?php echo $html->link('Stats', array('controller' => 'stashs','action'=>'stats', $myCollectibles[0]['Stash']['id'])); ?></li>
		    			
		    		</ul>	
		    	</div>
				<div class="glimpse">
					<?php foreach($myCollectibles[0]['CollectiblesUser'] as $myCollectible) {
						
						if (!empty($myCollectible['Collectible']['Upload'])) { 
							echo '<a href="/collections/viewCollectible/'.$myCollectible['id']. '">'.$fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => '100')).'</a>';
							echo '<div class="collectible image-fullsize hidden">';
							echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => 0));
							echo '</div>';
						 } else { 
							echo '<a href="/collections/viewCollectible/'.$myCollectible['id']. '"><img src="/img/silhouette_thumb.gif"/></a>';
					 } 
						
					}?>
					
				</div>
		    </div>    
		  </div>
		</div>
	</div>
	<div id="tabs-2">
		<?php 
		if($myCollection)
		{ ?>
		<div class="component">
		  <div class="inside">
		    <div class="component-title">
		      <h2><?php __('History');?></h2>
		    </div>
		    <div class="component-view">
		      <ul>
		        <li>You have <?php echo $submissionCount ?> collectibles awaiting to be approved.</li>
		      </ul>
		    </div>
		  </div>
		</div>
		<?php } ?>

	</div>
	<div id="tabs-3">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>
</div>






<div id="edit-stash-dialog" class="dialog" title="Edit Stash Details">
	<div class="component component-dialog">
	    <div class="inside" >
			<div class="component-info">
				<div><?php __('Edit the name of this stash.') ?></div> 
			</div>
	      	<div class="component-view">
	      		<?php echo $this->Form->create('Stash' ,array('id'=>'edit-stash-form','url' => array('controller'=>'stashs','action'=>'edit')));?>
    			<fieldset>
      				<ul class="form-fields">
        				<li>
        					 <div class="label-wrapper">
                				<label for="collectibleType"><?php __('Name') ?></label>
             				 </div>
        					<?php echo $this->Form->input('name', array('maxlength'=>'50','div' => false, 'label' => false,'id'=> 'editDialogStashName')); ?>
        				</li>
      				</ul>
      				<input type="hidden" id="editDialogStashId" name="data[Stash][id]" value="" />
    			</fieldset>
  				<?php echo $this->Form->end();?>
	      	</div>
	    </div>
	</div>	    
</div>

<div id="add-stash-dialog" class="dialog" title="Add New Stash">
	<div class="component component-dialog">
	    <div class="inside" >
			<div class="component-info">
				<div><?php __('Fill out the information below to add a new Stash.') ?></div> 
			</div>
	      	<div class="component-view">
	      		<?php echo $this->Form->create('Stash' ,array('id'=>'add-stash-form'));?>
    			<fieldset>
      				<ul class="form-fields">
        				<li>
        					 <div class="label-wrapper">
                				<label for="collectibleType"><?php __('Name') ?></label>
             				 </div>
        					<?php echo $this->Form->input('name', array('maxlength'=>'50','div' => false, 'label' => false,'id'=> 'addDialogStashName')); ?>
        				</li>
      				</ul>
    			</fieldset>
  				<?php echo $this->Form->end();?>
	      	</div>
	    </div>
	</div>	  
</div>

<div id="remove-stash-dialog" class="dialog" title="Remove Stash">
	<div class="component component-dialog">
	    <div class="inside" >
			<div class="component-info">
				<div><?php __('Are you sure you want to delete this stash?  All associated collectibles will also be deleted.') ?></div> 
			</div>
	      	<div class="component-view">
	      		<?php echo $this->Form->create('Stash' ,array('id'=> 'remove-stash-form','url' => array('controller'=>'stashs','action'=>'remove')));?>
    				<input type="hidden" id="removeDialogStashId" name="data[Stash][id]" value="" />
  				<?php echo $this->Form->end();?>
	      	</div>
	    </div>
	</div>	
</div>

<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>