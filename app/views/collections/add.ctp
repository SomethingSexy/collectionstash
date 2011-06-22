<?php echo $this->Html->script('collection-add',array('inline'=>false)); ?>
<?php echo $this->Html->script('jquery.form',array('inline'=>false)); ?>
<?php      
	echo $this->element('search_collectible',
	array("searchUrl" => '/collections/addSearch/stashId:'.$stashId));
?>
<div class="component" id="collectibles-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Add Collectible');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-view">
      <div class="collectibles view">
        <input type="hidden" id="stashId" value="<?php echo $stashId; ?>" />
        <?php  
        foreach ($collectibles as $collectible):
        ?>
        	<div class="collectible item">
				<?php echo $this -> element('collectible_list_image', array(
					'collectible' => $collectible
				));?>
				<?php echo $this -> element('collectible_list_detail', array(
					'collectible' => $collectible['Collectible'],
					'manufacture' => $collectible['Manufacture'],
					'license' => $collectible['License'],
					'collectibletype' => $collectible['Collectibletype']
				));?>
        		<div class="links">
	        	   <?php //At some point we should use Ajax to pull this data back. ?>
	        	   <input type="hidden" class="collectibleId" value='<?php echo $collectible['Collectible']['id']; ?>' />
	        	   <input type="hidden" class="showEditionSize" value='<?php echo $collectible['Collectible']['showUserEditionSize']; ?>' />
	        	   <a title="Add to stash" class="ui-icon ui-icon-plus add-to-collection">Add to Stash</a>
        	 </div>
        	 <div class="collectible actions"><?php echo $html->link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'])); ?></div>
          </div>
        <?php endforeach; ?>
        <div class="paging">
          <p>
          <?php
           echo $this->Paginator->counter(array(
           'format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)
           ));
          ?>  </p>
          <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
          <?php echo $this->Paginator->numbers();?>
          <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
        </div>
      
      </div>
    </div>
  </div>
</div>



<div id="add-collection-dialog" class="dialog" title="Add Collectible">
  <div class="component component-dialog">
    <div class="inside" >
      <div class="component-info">
        <div><?php __('Tell us about your collectible.') ?></div> 
      </div>
      <div class="component-view">
        <?php echo $this->Form->create('CollectiblesUser' ,array('url' => '/collections/add','id'=>'add-collection-form'));?>
          <fieldset>
            <ul class="form-fields">
              <li>
                <div class="label-wrapper">
                  <label for="dialogEditionSize"><?php __('Edition Number') ?></label>
                </div> 
                <?php echo $this->Form->input('edition_size', array('id'=>'dialogEditionSize','div' => false, 'label' => false)); ?>
              </li>
              <li>
                <div class="label-wrapper">
                  <label for="dialogCost"><?php __('How much did you pay? (USD)') ?></label>
                </div> 
                <?php echo $this->Form->input('cost', array('id'=>'dialogCost','div' =>  false, 'label' => false));?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserConditionId"><?php __('Condition') ?></label>
                </div> 
                <?php echo $this->Form->input('condition_id', array('div' =>  false, 'label' => false));?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserMerchantId"><?php __('Where did you purchase the collectible?') ?></label>
                </div> 
                <?php echo $this->Form->input('merchant_id', array('div' =>  false, 'label' => false));?>
              </li> 
              <input type="hidden" id="collectiblesStashId" name="data[CollectiblesUser][stash_id]" value="" />
              <input type="hidden" id="collectiblesStashCollectibleId" name="data[CollectiblesUser][collectible_id]" value="" />
            </ul>
          </fieldset>
        <?php echo $this->Form->end();?>          
      </div>
    </div>
  </div>
</div>
