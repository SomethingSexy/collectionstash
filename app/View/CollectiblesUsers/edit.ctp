<?php echo $this->Html->script('collection-edit',array('inline'=>false)); ?>
<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Edit Your Collectible');?></h2>
    </div>
    <?php echo $this -> element('flash');?>
    <div class="component-info">
      <div><?php __('Edit the information about the collectible in your personal collection.  This update will not change the base collectible but just he one linked in your collection.');?></div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('CollectiblesUser',array('action'=>'edit'));?>
        <fieldset>
          <ul class="form-fields unstyled">
          	<?php 
          		$editionSize = $collectible['Collectible']['edition_size'];
				if($collectible['Collectible']['numbered'])
				{ ?>
            <li>
              <div class="label-wrapper">
                <label for="collectibleType"><?php echo __('Edition Number')?> (Total: <?php echo $collectible['Collectible']['edition_size'] ?> )</label>
              </div>
              <?php  echo $this->Form->input('edition_size', array('div' => false, 'label'=>false));  ?>
            </li>
            <?php } ?>
	          <li>
	            <div class="label-wrapper">
	              <label for="dialogCost"><?php echo __('Artist\'s Proof') ?></label>
	            </div> 
	            <?php echo $this->Form->input('artist_proof', array('div' =>  false, 'label' => false));?>
	          </li>
			<li>
                <div class="label-wrapper">
                    <label for="dialogCost"><?php echo __('How much did you pay?') ?> (Retail: <?php echo $collectible['Collectible']['Currency']['sign']; echo $collectible['Collectible']['msrp'] ?> )</label>
                </div> 
                <?php echo $this->Form->input('cost', array('id'=>'dialogCost','div' => false, 'label' => false));?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserConditionId"><?php echo __('Condition') ?></label>
                </div> 
               	<?php echo $this -> Form -> input('condition_id', array('div' => false, 'label' => false, 'empty' => true)); ?>
              </li> 
              <li>
                <div class="label-wrapper">
                  <label for="CollectiblesUserMerchantId"><?php echo __('Where did you purchase the collectible?') ?></label>
                </div> 
                <?php echo $this -> Form -> input('merchant', array('type'=> 'text', 'div' => false, 'label' => false, 'maxLength' => 150)); ?>
              </li> 
	          <li>
	            <div class="label-wrapper">
	              <label for=""><?php echo __('When did you purchase this collectible?') ?></label>
	            </div> 
	            <?php echo $this->Form->text('purchase_date', array('div' =>  false, 'label' => false ,'maxLength'=>8));?>
	          </li>               
          </ul>
          <input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id'] ?>" />
        </fieldset>
        <input type="submit" value="Submit" class="btn btn-primary">
      <?php echo $this->Form->end();?>
          </div>    
  </div>
</div>
<script><?php
echo 'var merchants=[';

foreach ($merchants as $key => $value) {
	echo '\''.addslashes($value['Merchant']['name']).'\'';
	if ($key != (count($merchants) - 1)) {
		echo ',';
	}
}
echo '];';
?>
	$(function() {
		$("#CollectiblesUserPurchaseDate").datepicker();
		var options, a;
		jQuery(function() {
			options = {
				lookup : merchants,
				width : 282,
				onSelect : function(value, data) {
					// Not sure I need to do anything here
				}
			};
			a = $('#CollectiblesUserMerchant').autocomplete(options);
		});

	});

</script>