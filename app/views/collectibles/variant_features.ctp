<?php echo $this -> Html -> script('variant-add', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features', 'tags'=>'Tags','image'=>'Image', 'review'=> 'Review')); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Add New Collectible', true); ?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>__</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('url' => $this->here)); ?>
						<fieldset>
						<legend>
							<?php __('Variant Details');?>
						</legend>
						<ul class="form-fields">
							<li>
								<div class="label-wrapper">
									<label for="CollectibleExclusive">
										<?php __('Exclusive') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('exclusive', array('div' => false, 'label' => false));?>
							</li>	
							<li>
								<div class="label-wrapper">
									<label for="">
										<?php __('Exclusive Retailer') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('retailer_id', array('div' => false, 'label' => false, 'empty' => true));?>
							</li>					
						</ul>
					</fieldset>					
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>
<script>
	$(".ui-icon-info").tooltip({
		position: 'center right',
		opacity: 0.7
	});
</script>