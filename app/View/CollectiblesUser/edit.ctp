<?php echo $this->Html->script('collection-edit',array('inline'=>false)); ?>
<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Edit Your Collectible');?></h2>
    </div>
    <?php echo $this -> element('flash');?>
    <div class="component-info">
      <div><?php __('Edit the information about the collectible in your personal collection.  This update will not change the base collectible but just he one linked in your collection.');?></div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('CollectiblesUser' , array('url' => array('controller'=>'collectibles_user','action'=>'edit', $collectible['CollectiblesUser']['id'])));?>
        <fieldset>
          <ul class="form-fields">
          	<?php 
          		$editionSize = $collectible['Collectible']['edition_size'];
				if($collectible['Collectible']['numbered'])
				{ ?>
            <li>
              <div class="label-wrapper">
                <label for="collectibleType"><?php __('Edition Number')?> (Total: <?php echo $collectible['Collectible']['edition_size'] ?> )</label>
              </div>
              <?php  echo $this->Form->input('edition_size', array('div' => false, 'label'=>false));  ?>
            </li>
            <?php } ?>
			<li>
                <div class="label-wrapper">
                  <label for="dialogCost"><?php __('How much did you pay?') ?> (Retail: $<?php echo $collectible['Collectible']['msrp'] ?> )</label>
                </div> 
                <?php echo $this->Form->input('cost', array('id'=>'dialogCost','div' => false, 'label' => false));?>
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
	          <li>
	            <div class="label-wrapper">
	              <label for=""><?php __('When did you purchase this collectible?') ?></label>
	            </div> 
	            <?php echo $this->Form->text('purchase_date', array('div' =>  false, 'label' => false ,'maxLength'=>8));?>
	          </li>               
          </ul>
          <input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id'] ?>" />
        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>
          </div>    
  </div>
</div>
<script>
	$(function() {
		$( "#CollectiblesUserPurchaseDate" ).datepicker();
	});
</script>