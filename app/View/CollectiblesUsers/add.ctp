<div class="component">
	<div class="inside" >
		<div class="component-title">
			<h2><?php echo __('Add to Stash')
			?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-info">
			<div>
				<?php echo __('Tell us about your collectible.')
				?>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('CollectiblesUser'); ?>
			<fieldset>
				<ul class="form-fields">
					<?php
$editionSize = $collectible['Collectible']['edition_size'];
if($collectible['Collectible']['numbered'])
{
					?>
					<li>
						<div class="label-wrapper">
							<label for="collectibleType"><?php echo __('Edition Number')
								?>(Total: <?php echo $collectible['Collectible']['edition_size']
								?>)</label>
						</div>
						<?php  echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false)); ?>
					</li>
					<?php } ?>
					<li>
						<div class="label-wrapper">
							<label for="dialogCost"><?php echo __('Artist\'s Proof')
								?></label>
						</div>
						<?php echo $this -> Form -> input('artist_proof', array('div' => false, 'label' => false)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="dialogCost"><?php echo __('How much did you pay?')
								?>(Retail: <?php echo $collectible['Currency']['sign']; echo $collectible['Collectible']['msrp']
								?>)</label>
						</div>
						<?php echo $this -> Form -> input('cost', array('id' => 'dialogCost', 'div' => false, 'label' => false)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="CollectiblesUserConditionId"><?php echo __('Condition')
								?></label>
						</div>
						<?php echo $this -> Form -> input('condition_id', array('div' => false, 'label' => false)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="CollectiblesUserMerchantId"><?php echo __('Where did you purchase the collectible?')
								?></label>
						</div>
						<?php echo $this -> Form -> input('merchant', array('type'=> 'text', 'div' => false, 'label' => false, 'maxLength' => 150)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for=""><?php echo __('When did you purchase this collectible?')
								?></label>
						</div>
						<?php echo $this -> Form -> text('purchase_date', array('div' => false, 'label' => false, 'maxLength' => 8)); ?>
					</li>
					<?php echo $this -> Form -> hidden('CollectiblesUser.collectible_id'); ?>
				</ul>
			</fieldset>
			<?php echo $this -> Form -> end('Add'); ?>
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