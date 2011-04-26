<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Edit Your Collectible');?></h2>
    </div>
    <div class="component-info">
      <div><?php __('Edit the information about the collectible in your personal collection.  This update will not change the base collectible but just he one linked in your collection.');?></div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('CollectiblesUser' , array('type' => 'file', 'url' => array('controller'=>'collections','action'=>'editCollectible')));?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for="collectibleType"><?php __('Edition Size')?> (Total: <?php echo $collectible['Collectible']['edition_size'] ?> )</label>
              </div>
              <?php  echo $this->Form->input('edition_size', array('div' => false, 'label'=>false, 'value'=>$collectible['CollectiblesUser']['edition_size']));  ?>
            </li>
			<li>
                <div class="label-wrapper">
                  <label for="dialogCost"><?php __('How much did you pay?') ?></label>
                </div> 
                <?php echo $this->Form->input('cost', array('id'=>'dialogCost','div' =>  false,'value'=>$collectible['CollectiblesUser']['cost'], 'label' => false));?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserConditionId"><?php __('Condition') ?></label>
                </div> 
                <?php echo $this->Form->input('condition_id', array('div' =>  false, 'label' => false, 'selected' =>$collectible['CollectiblesUser']['condition_id']));?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserMerchantId"><?php __('Where did you purchase the collectible?') ?></label>
                </div> 
                <?php echo $this->Form->input('merchant_id', array('div' =>  false, 'label' => false, 'selected' =>$collectible['CollectiblesUser']['merchant_id']));?>
              </li> 
          </ul>
          <input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id'] ?>" />
          <input type="hidden" name="data[CollectiblesUser][collectible_id]" value="<?php echo $collectible['CollectiblesUser']['collectible_id'] ?>" />
        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>
          </div>    
  </div>
</div>
