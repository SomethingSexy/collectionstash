<?php echo $this -> Html -> script('collection-edit', array('inline' => false)); ?>
<div class="well">
	<div class="page-header">
		<h1><?php echo __('Edit Your Collectible'); ?></h1>
	</div>
	<?php echo $this -> element('flash'); ?>
	<p><?php echo __('Edit the information about the collectible in your personal collection.  This update will not change the base collectible but just the one linked in your collection.'); ?></p> 
	<p>
	<?php
	if ($collectible['CollectiblesUser']['active']) {
		echo __('This collectible is active.');
	} else {
		echo __('This collectible is inactive.');
	}
	?>
	</p>
	<?php echo $this -> Form -> create('CollectiblesUser', array('action' => 'edit', 'class' => 'form-horizontal')); ?>
	<fieldset>
	  	<?php 
	  		$editionSize = $collectible['Collectible']['edition_size'];
			if($collectible['Collectible']['numbered'])
			{ ?>
				<div class="control-group">
	        		<label class="control-label" for="collectibleType"><?php echo __('Edition Number')?> (Total: <?php echo $collectible['Collectible']['edition_size'] ?> )</label>
	    			<div class="controls">
	      				<?php  echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false)); ?>
	      			</div>
				</div>
	    <?php } ?>
		<div class="control-group">
			<label class="control-label" for="dialogCost"><?php echo __('Artist\'s Proof') ?></label>
			<div class="controls">
			<?php echo $this -> Form -> input('artist_proof', array('div' => false, 'label' => false)); ?>
			</div>
		</div>
		<div class="control-group">
	    	<label class="control-label" for="dialogCost"><?php echo __('How much did you pay?') ?><?php
			if ($collectible['Collectible']['msrp']) {
				$currencySign = '$';
				if (isset($collectible['Currency'])) {
					$currencySign = $collectible['Currency']['sign'];
				}

				echo '(Retail:' . $currencySign;
				echo $collectible['Collectible']['msrp'] . ')';
			}
			?> </label>
	        <div class="controls">
	        <?php echo $this -> Form -> input('cost', array('id' => 'dialogCost', 'div' => false, 'label' => false)); ?>
			</div>
		</div>
		<div class="control-group">
	          <label class="control-label" for="CollectiblesUserConditionId"><?php echo __('Condition') ?></label>
	       <div class="controls">
	       	<?php echo $this -> Form -> input('condition_id', array('div' => false, 'label' => false, 'empty' => true)); ?>
			</div>
		</div>
		<div class="control-group">
	          <label class="control-label" for="CollectiblesUserMerchantId"><?php echo __('Where did you purchase the collectible?') ?></label>
	        <div class="controls">
	        <?php echo $this -> Form -> input('merchant', array('type' => 'text', 'div' => false, 'label' => false, 'maxLength' => 150)); ?>
			</div>
		</div> 
		<div class="control-group">
	          <label class="control-label" for=""><?php echo __('When did you purchase this collectible?') ?></label>
	      <div class="controls">
	        <?php echo $this -> Form -> text('purchase_date', array('div' => false, 'label' => false, 'maxLength' => 8)); ?>
	  			</div>
		</div>  
		<?php if (!$collectible['CollectiblesUser']['active']) { 
				if($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '1') {	?>
					<div class="control-group ">
						<label for="CollectiblesUserSoldCost" class="control-label">How much did you sell it for?</label>
						<div class="controls">
							 <?php echo $this -> Form -> input('sold_cost', array('type' => 'number', 'div' => false, 'label' => false, 'maxLength' => 23)); ?>
						</div>
					</div>
				
			
				<?php } ?>
				<div class="control-group ">
					<label for="CollectiblesUserRemoveDate" class="control-label"> When did you sell this collectible? </label>
					<div class="controls">
						<?php echo $this -> Form -> text('remove_date', array('div' => false, 'label' => false, 'maxLength' => 8, 'required')); ?>
					</div>
				</div>
		<?php } ?>           
		<input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id'] ?>" />
	</fieldset>
	<div class="form-actions">
		<input type="submit" value="Save" class="btn btn-primary">
	</div>
	<?php echo $this -> Form -> end(); ?>
</div>
<script><?php
echo 'var merchants=[';

foreach ($merchants as $key => $value) {
	echo '\'' . addslashes($value['Merchant']['name']) . '\'';
	if ($key != (count($merchants) - 1)) {
		echo ',';
	}
}
echo '];';
?>
	$(function() {
		$("#CollectiblesUserPurchaseDate").datepicker();
		$("#CollectiblesUserRemoveDate").datepicker();		
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