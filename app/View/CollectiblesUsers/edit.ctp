<?php echo $this -> Html -> script('collection-edit', array('inline' => false)); ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h1 class="panel-title"><?php echo __('Edit Your Collectible'); ?></h1>
	</div>
	 <div class="panel-body">
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
				<div class="form-group">
	        		<label class="control-label col-lg-3" for="collectibleType"><?php echo __('Edition Number')?> (Total: <?php echo $collectible['Collectible']['edition_size'] ?> )</label>
	    			<div class="col-lg-6">
	      				<?php  echo $this -> Form -> input('edition_size', array('class' => 'form-control', 'div' => false, 'label' => false)); ?>
	      			</div>
				</div>
	    <?php } ?>
		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-6">
				<div class="checkbox">
					<label>
						<?php echo $this -> Form -> input('artist_proof', array('div' => false, 'label' => false)); ?>
						<?php echo __('Artist\'s Proof') ?></label>
				</div>
			</div>
		</div>
		<div class="form-group">
	    	<label class="control-label col-lg-3" for="dialogCost"><?php echo __('How much did you pay?') ?><?php
			if ($collectible['Collectible']['msrp']) {
				$currencySign = '$';
				if (isset($collectible['Currency'])) {
					$currencySign = $collectible['Currency']['sign'];
				}

				echo '(Retail:' . $currencySign;
				echo $collectible['Collectible']['msrp'] . ')';
			}
			?> </label>
	        <div class="col-lg-6">
	        <?php echo $this -> Form -> input('cost', array('class' => 'form-control', 'id' => 'dialogCost', 'div' => false, 'label' => false)); ?>
			</div>
		</div>
		<div class="form-group">
	          <label class="control-label col-lg-3" for="CollectiblesUserConditionId"><?php echo __('Condition') ?></label>
	       <div class="col-lg-6">
	       	<?php echo $this -> Form -> input('condition_id', array('class' => 'form-control', 'div' => false, 'label' => false, 'empty' => true)); ?>
			</div>
		</div>
		<div class="form-group">
	          <label class="control-label col-lg-3" for="CollectiblesUserMerchantId"><?php echo __('Where did you purchase the collectible?') ?></label>
	        <div class="col-lg-6">
	        <?php echo $this -> Form -> input('merchant', array('class' => 'form-control', 'type' => 'text', 'div' => false, 'label' => false, 'maxLength' => 150)); ?>
			</div>
		</div> 
		<div class="form-group">
	          <label class="control-label col-lg-3" for=""><?php echo __('When did you purchase this collectible?') ?></label>
	      <div class="col-lg-6">
	        <?php echo $this -> Form -> text('purchase_date', array('class' => 'form-control', 'div' => false, 'label' => false, 'maxLength' => 10)); ?>
	  			</div>
		</div>  
		<?php if (!$collectible['CollectiblesUser']['active']) { 
				if($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '1') {	?>
					<div class="form-group ">
						<label for="CollectiblesUserSoldCost col-lg-3" class="control-label">How much did you sell it for?</label>
						<div class="col-lg-6">
							 <?php echo $this -> Form -> input('sold_cost', array('class' => 'form-control', 'type' => 'number', 'div' => false, 'label' => false, 'maxLength' => 23)); ?>
						</div>
					</div>
				
			
				<?php } ?>
				<div class="form-group ">
					<label for="CollectiblesUserRemoveDate col-lg-3" class="control-label"> When did you sell this collectible? </label>
					<div class="col-lg-6">
						<?php echo $this -> Form -> text('remove_date', array('class' => 'form-control', 'div' => false, 'label' => false, 'maxLength' => 10, 'required')); ?>
					</div>
				</div>
		<?php } ?>           
		<input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id'] ?>" />
	</fieldset>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
		<input type="submit" value="Save" class="btn btn-primary">
	</div>
	</div>
	<?php echo $this -> Form -> end(); ?>
	</div>
</div>
<script>
	$(function() {
		$("#CollectiblesUserPurchaseDate").datepicker();
		$("#CollectiblesUserRemoveDate").datepicker();
		
		$('#CollectiblesUserMerchant', this.el).typeahead({
			name : 'merchants',
			remote: '/merchants/getMerchantList?query=%QUERY',
		});

	});

</script>