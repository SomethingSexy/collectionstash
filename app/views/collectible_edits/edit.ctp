<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<?php if($this -> Session -> check('edit.collectible.mode.variant')) {
?>
<?php
	echo $this -> element('collectible_detail', array('title' => __('Base Collectible Details', true), 'showStatistics' => false, 'showWho' => false, 'collectibleDetail' => $collectible));
?>
<?php }?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Edit Collectible');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Text here
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php
				echo $this -> Form -> create('Collectible', array('id' => 'edit-manufacture-form', 'url' => '/collectible_edits/edit/'));
				?>
				<fieldset>
					<legend>
						<?php __('Details');?>
					</legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for=""> <?php __('Manufacture')
									?></label>
							</div>
							<?php echo $this -> Form -> input('manufacture_id', array('div' => false, 'label' => false));?>
						</li>
						<?php
						if (empty($licenses)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php __('Brand/License')
								?></label>
						</div>
						<?php echo $this -> Form -> input('license_id', array('div' => false, 'label' => false));?>
						</li>

						<?php
						if (empty($series)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php __('Category')
								?></label>
						</div>
						<?php echo $this -> Form -> input('series_id', array('div' => false, 'label' => false));?>
						</li>

						<?php
						if (empty($collectibletypes)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php __('What type of collectible is this?')
								?></label>
						</div>
						<?php  echo $this -> Form -> select('Collectible.collectibletype_id', $collectibletypes, null, array('label' => false, 'empty' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale"> <?php __('Scale')
									?></label>
							</div>
							<?php echo $this -> Form -> input('scale_id', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale"> <?php __('Release Year')
									?></label>
							</div>
							<?php echo $this -> Form -> year('release', 1900, date('Y'));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleName"> <?php __('Name')
									?></label>
							</div>
							<?php echo $this -> Form -> input('name', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleDescription"> <?php __('Description')
									?></label>
							</div>
							<?php
							$data = str_replace('\n', "\n", $this -> data['Collectible']['description']);
							$data = str_replace('\r', "\r", $data);

							echo $this -> Form -> input('description', array('div' => false, 'label' => false, 'value' => $data));
							?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleMsrp"> <?php __('Original Retail Price (USD)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('msrp', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUrl"> <?php __('URL')
									?></label>
							</div>
							<?php echo $this -> Form -> input('url', array('div' => false, 'label' => false, 'escape'=>false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleEditionSize"> <?php __('Limited Edition?')
									?></label>
							</div>
							<?php echo $this -> Form -> input('limited', array('div' => false, 'label' => false));?>
						</li>
						<?php
							if ($this -> data['Collectible']['limited']) {
								echo '<li>';
							} else {
								echo '<li class="hidden">';
							}
						?>
						<div class="label-wrapper">
							<label for="CollectibleEditionSize"> <?php __('Edition Size')
								?></label>
							<a class="ui-icon ui-icon-info" title="<?php echo __('This is the edition size of the collectible.  If it is unknown or it does not have a specific edition size, leave blank.', true) ?>" alt="info"></a>
						</div>
						<?php echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false));?>
						</li>
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
						<li>
							<div class="label-wrapper">
								<label for="CollectibleCode"> <?php __('Product Code')
									?></label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('This is the item code or product code given by the manufacture.', true) ?>" alt="info"></a>
							</div>
							<?php echo $this -> Form -> input('code', array('div' => false, 'label' => false, 'between' => ''));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUpc"> <?php __('Product UPC')
									?></label>
							</div>
							<?php echo $this -> Form -> input('upc', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleProductWeight"> <?php __('Weight (lbs)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_weight', array('div' => false, 'label' => false));?>
						</li>
						<!--<div class="dimensions"><?php echo $this -> Form -> input('product_length', array('div' => false, 'size'=> '10', 'label' => false, 'id' => 'collectibleHeight'));?><span> x </span><?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?><span> x </span><?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?></div>-->
						<li>
							<div class="label-wrapper">
								<label for="collectibleHeight"> <?php __('Height (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_length', array('div' => false, 'label' => false, 'id' => 'collectibleHeight'));?>
						</li>
						<li id="widthWrapper">
							<div class="label-wrapper">
								<label for="collectibleWidth"> <?php __('Width (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?>
						</li>
						<li id="depthWrapper">
							<div class="label-wrapper">
								<label for="collectibleDepth"> <?php __('Depth (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end();?>
				<?php echo $this -> Form -> create('Collectible', array('url' => '/collectibles/view/'.$currentCollectibleId, 'id' => 'skip-manufacture-form'));?>
					<input type="hidden" name="data[skip]" value="true" />
				</form>
				<div class="links">
					<input type="button" id="edit-manufacture-button" class="button" value="Submit">
					<input type="button" id="skip-manufacture-button" class="button" value="Cancel">
				</div>
				<script>
					$(function() {
						//Eh move this out of here
						$('#edit-manufacture-button').click(function() {
							$('#edit-manufacture-form').submit();
						});
						$('#skip-manufacture-button').click(function() {
							$('#skip-manufacture-form').submit();
						});
					});

				</script>
			</div>
		</div>
	</div>
</div>
<script>
	$(".ui-icon-info").tooltip({
		position : 'center right',
		opacity : 0.7
	});

</script>