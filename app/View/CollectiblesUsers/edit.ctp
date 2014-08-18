<?php 
echo $this -> Html -> script('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> css('/bower_components/select2/select2');
echo $this -> Html -> script('collection-edit', array('inline' => false)); ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h1 class="panel-title"><?php echo __('Edit Your Collectible'); ?></h1>
	</div>
	 <div class="panel-body">
	<?php echo $this -> element('flash'); ?>
	<p><?php echo __('Edit the information about the collectible in your personal collection.  This update will not change the base collectible but just the one linked in your collection.'); ?></p> 
	
	<?php
	if ($collectible['CollectiblesUser']['active']) {
		echo '<div class="alert alert-info"><p>' . __('This collectible is active.') . '</p></div>';
	} else {
		echo '<div class="alert alert-warning"><p>' . __('This collectible is inactive.') . '</p></div>';
	}
	?>
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
	          <label class="control-label col-lg-3" for=""><?php echo __('Where did you purchase the collectible?') ?></label>
	        <div class="col-lg-6">
	        	<input type="hidden" class="bigdrop select2-offscreen merchants-typeahead" value="<?php echo $this -> data['CollectiblesUser']['merchant_id']; ?>" style="width: 100%;" name="" tabindex="-1" title="">
			</div>
		</div> 
		<div class="form-group">
	          <label class="control-label col-lg-3" for=""><?php echo __('When did you purchase this collectible?') ?></label>
	      <div class="col-lg-6">
	        <?php echo $this -> Form -> text('purchase_date', array('class' => 'form-control', 'div' => false, 'label' => false, 'maxLength' => 10)); ?>
	  			</div>
		</div>  
		<div class="form-group">
			<label class="control-label col-lg-3" for="CollectiblesUserNotes">Notes</label>
			<div class="col-lg-6">
				<?php
				$value = str_replace('\n', "\n", $this -> data['CollectiblesUser']['notes']);
				$value = str_replace('\r', "\r", $value);
				$vaule = html_entity_decode($value);
				?>
				<textarea id="CollectiblesUserNotes" class="form-control" maxlength="1000" name="data[CollectiblesUser][notes]"><?php echo $value; ?></textarea>
			</div>
		</div>	
		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-6">
				<div class="checkbox">
					<label>
						<?php echo $this -> Form -> input('notes_private', array('div' => false, 'label' => false)); ?>
						<?php echo __('Private Notes') ?></label>
				</div>
			</div>
		</div>
		<?php if (!$collectible['CollectiblesUser']['active']) { 
				if($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '1') {	?>
					<div class="form-group ">
						<label for="CollectiblesUserSoldCost" class="col-lg-3 control-label">How much did you sell it for?</label>
						<div class="col-lg-6">
							 <?php echo $this -> Form -> input('sold_cost', array('class' => 'form-control', 'type' => 'number', 'div' => false, 'label' => false, 'maxLength' => 23)); ?>
						</div>
					</div>
					<input type="hidden" name="data[CollectiblesUser][listing_type_id]" value="2" />  
				<?php } else if ($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '2') { ?>
					<div class="form-group ">
						<label for="CollectiblesUserTradedFor" class="col-lg-3 control-label">What did you trade this collectible for?</label>
						<div class="col-lg-6">
							<?php
							$value = '';
							if (isset($this -> data['CollectiblesUser']['traded_for'])) {
								$value = str_replace('\n', "\n", $this -> data['CollectiblesUser']['traded_for']);
								$value = str_replace('\r', "\r", $value);
								$vaule = html_entity_decode($value);
							}?>
							<textarea id="CollectiblesUserTradedFor" class="form-control" maxlength="1000" name="data[CollectiblesUser][traded_for]"><?php echo $value; ?></textarea>
						</div>
					</div>	
					<input type="hidden" name="data[CollectiblesUser][listing_type_id]" value="3" />  			
				<?php } ?>
				<div class="form-group ">
					<label for="CollectiblesUserRemoveDate" class="col-lg-3 control-label"> When did you sell this collectible? </label>
					<div class="col-lg-6">
						<?php echo $this -> Form -> text('remove_date', array('class' => 'form-control', 'div' => false, 'label' => false, 'maxLength' => 10, 'required')); ?>
					</div>
				</div>
		<?php } else { ?>        
			<?php if(isset($collectible['CollectiblesUser']['sold_cost'] )) { ?>
				<input type="hidden" name="data[CollectiblesUser][sold_cost]" value="<?php echo $collectible['CollectiblesUser']['sold_cost'] ?>" />  
			<?php }?>
			<?php if(isset($collectible['CollectiblesUser']['traded_for'] )) { ?>
				<input type="hidden" name="data[CollectiblesUser][traded_for]" value="<?php echo $collectible['CollectiblesUser']['traded_for'] ?>" />  
			<?php }?>
			<?php if(isset($collectible['CollectiblesUser']['remove_date'] )) { ?>
				<input type="hidden" name="data[CollectiblesUser][remove_date]" value="<?php echo $collectible['CollectiblesUser']['remove_date'] ?>" />  
			<?php }?>					
		<?php } ?> 
		<input type="hidden" name="data[CollectiblesUser][sale]" value="<?php echo $collectible['CollectiblesUser']['sale']; ?>" />  
		<input type="hidden" name="data[CollectiblesUser][id]" value="<?php echo $collectible['CollectiblesUser']['id']; ?>" />
		<input type="hidden" name="data[CollectiblesUser][merchant] "id="CollectiblesUserMerchantValue" value="<?php if(isset($this -> data['CollectiblesUser']['merchant'])) {echo $this -> data['CollectiblesUser']['merchant'];} ?>" />
	</fieldset>
	<div class="form-group">
		<div class="col-lg-offset-3 col-lg-9">
		<input type="submit" value="Save" class="btn btn-primary">
	</div>
	</div>
	<?php echo $this -> Form -> end(); ?>
	</div>
</div>