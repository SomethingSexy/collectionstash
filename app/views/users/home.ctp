<?php echo $this->Html->script('jquery.form',array('inline'=>false)); ?>
<?php echo $this->Html->script('stash',array('inline'=>false)); ?>
<?php echo $this->Html->script('user-home',array('inline'=>false)); ?>

<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('My Stashes');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
        <div>You have <?php echo $stashCount ?> <?php if($stashCount==1){echo 'stash';}else{echo 'stashes';} ?>. <a class="add-stash link"><?php __('Add new Stash');?></a>   </div>
    </div>
    <div class="component-view">
      <div id="stash-list-container">
         <?php foreach($stashDetails as $details) {?>
           <h3><a href="#"><?php echo $details['Stash']['name']; ?></a></h3>
           <div class="stash-list-details">
              <div><?php __('There are '); echo $details['Stash']['count']; __(' collectibles in this stash.'); ?></div>
              <div class="stash-actions"><?php echo $html->link('View', array('controller' => 'stashs',$details['Stash']['id'])); ?> | <?php echo $html->link('Add', array('controller' => 'collections','action'=>'addSearch', 'stashId' => $details['Stash']['id'],'initial'=>'yes')); ?> | <a class="edit-stash link">Edit</a> | <a class="remove-stash link">Remove</a> | <?php echo $html->link('Stats', array('controller'=>'stashs', 'action' => 'stats',$details['Stash']['id'])); ?></div>
              <input type="hidden" class="stashId" value="<?php echo $details['Stash']['id']; ?>" />
           </div>
        <?php } ?>
       </div>   

    </div>    
  </div>
</div>

<?php 
if($myCollection)
{ ?>
<div class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('My Submissions');?></h2>
    </div>
    <div class="component-view">
      <ul>
        <li>You have <?php echo $submissionCount ?> collectibles awaiting to be approved.</li>
      </ul>
    </div>
  </div>
</div>
<?php } ?>


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