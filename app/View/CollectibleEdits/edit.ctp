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
				echo $this -> Form -> hidden('variant');
				echo $this -> Form -> hidden('variant_collectible_id');
				?>
				<fieldset>
					<legend>
						<?php echo __('Details');?>
					</legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for=""> <?php echo __('Manufacture')
									?></label>
							</div>
							<?php
							echo '<div class="static-field">';
							echo $manufacturer['Manufacture']['title'];
							echo $this -> Form -> hidden('manufacture_id');
							echo '</div>';
							?>
						</li>
						<?php echo '<li>';?>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Collectible Type');
								?></label>
						</div>
						<?php
						//Ok for now, lets draw out the first list, then check for the second list...will manually set which one is selected based on
						//$selectedTypes, then we will see if a L2 is list is set and draw that one.  Then we will need to update the JavaScript for this
						//page that determine which one is selected to put in teh input field...
						//OR
						//We use a modal dialog to change the type, and then the specialized type..., might be easier than trying to draw this on one page
						//logic will be similar.
						//Open up the modal, call a collectibletypes_getTypes ajax action, pass in manufacture id and selected collectible type, this will
						//return each level of lists and which ones are selected, then once they select, change it on the page and submit...BAM

						//echo $this -> Form -> select('Collectible.collectibletype_id', $collectibletypes, null, array('label' => false, 'empty' => false));
						echo '<div class="static-field">';
						echo '<a class="link" id="change-collectibletype-link">' . $collectibleType['Collectibletype']['name'] . '</a>';
						echo $this -> Form -> hidden('collectibletype_id');
						echo '</div>';
						?>
						</li>
						<?php
						if (!isset($specializedTypes)) {
							$specializedTypes = array();
						}

						if (empty($specializedTypes)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Manufacturer Collectible Type');
								?></label>
						</div>
						<?php  echo $this -> Form -> select('Collectible.specialized_type_id', $specializedTypes, array('label' => false, 'empty' => true));?>
						</li>
						<?php
						if (empty($hasSeries)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Category')
								?></label>
						</div>
						<?php
						if (isset($this -> data['Collectible']['series_id']) && !empty($this -> data['Collectible']['series_id'])) {
							echo '<div class="static-field">';
							echo '<a class="link" id="change-series-link">' . $this -> data['Collectible']['series_name'] . '</a>';
							echo $this -> Form -> hidden('series_id');
							echo '</div>';
							echo $this -> Form -> error('series_id');
						} else {
							echo '<div class="static-field">';
							echo '<a class="link" id="change-series-link">Add</a>';
							echo $this -> Form -> hidden('series_id');
							echo '</div>';
							echo $this -> Form -> error('series_id');
						}
						?>
						</li>						
						
						<?php
						if (empty($licenses)) {
							echo '<li class="hidden">';
						} else {
							echo '<li>';
						}
						?>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Brand/License')
								?></label>
						</div>
						<?php echo $this -> Form -> input('license_id', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale"> <?php echo __('Scale')
									?></label>
							</div>
							<?php echo $this -> Form -> input('scale_id', array('empty' => true, 'div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale"> <?php echo __('Release Year')
									?></label>
							</div>
							<?php
							$current_year = date('Y');
							$max_year = $current_year + 2;
							echo $this -> Form -> year('release', 1900, $max_year);
							?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleName"> <?php echo __('Name')
									?></label>
							</div>
							<?php echo $this -> Form -> input('name', array('escape' => false, 'div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleDescription"> <?php echo __('Description')
									?></label>
							</div>
							<?php
							$data = str_replace('\n', "\n", $this -> data['Collectible']['description']);
							$data = str_replace('\r', "\r", $data);

							echo $this -> Form -> input('description', array('escape' => false, 'div' => false, 'label' => false, 'value' => $data));
							?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleMsrp"> <?php echo __('Original Retail Price (USD)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('msrp', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUrl"> <?php echo __('URL')
									?></label>
							</div>
							<?php echo $this -> Form -> input('url', array('escape' => false, 'div' => false, 'label' => false, 'escape' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleEditionSize"> <?php echo __('Limited Edition?')
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
							<label for="CollectibleEditionSize"> <?php echo __('Edition Size')
								?></label>
							<a class="ui-icon ui-icon-info" title="<?php echo __('This is the edition size of the collectible.  If it is unknown or it does not have a specific edition size, leave blank.', true) ?>" alt="info"></a>
						</div>
						<?php echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false));?>
						</li>
						<?php
							if ($this -> data['Collectible']['limited']) {
								echo '<li>';
							} else {
								echo '<li class="hidden">';
							}
						?>
						<div class="label-wrapper">
							<label for="CollectibleNumbered"> <?php echo __('Numbered')
								?></label>
							<a class="ui-icon ui-icon-info" title="<?php echo __('A collectible is considered numbered if it has an edition size and is indivudually numbered.', true) ?>" alt="info"></a>
						</div>
						<?php echo $this -> Form -> input('numbered', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleExclusive"> <?php echo __('Exclusive')
									?></label>
							</div>
							<?php echo $this -> Form -> input('exclusive', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for=""> <?php echo __('Exclusive Retailer')
									?></label>
							</div>
							<?php echo $this -> Form -> input('retailer_id', array('div' => false, 'label' => false, 'empty' => true));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleCode"> <?php echo __('Product Code')
									?></label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('This is the item code or product code given by the manufacture.', true) ?>" alt="info"></a>
							</div>
							<?php echo $this -> Form -> input('code', array('div' => false, 'label' => false, 'between' => ''));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUpc"> <?php echo __('Product UPC')
									?></label>
							</div>
							<?php echo $this -> Form -> input('upc', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleProductWeight"> <?php echo __('Weight (lbs)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_weight', array('div' => false, 'label' => false));?>
						</li>
						<!--<div class="dimensions"><?php echo $this -> Form -> input('product_length', array('div' => false, 'size'=> '10', 'label' => false, 'id' => 'collectibleHeight'));?><span> x </span><?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?><span> x </span><?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?></div>-->
						<li>
							<div class="label-wrapper">
								<label for="collectibleHeight"> <?php echo __('Height (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_length', array('div' => false, 'label' => false, 'id' => 'collectibleHeight'));?>
						</li>
						<li id="widthWrapper">
							<div class="label-wrapper">
								<label for="collectibleWidth"> <?php echo __('Width (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?>
						</li>
						<li id="depthWrapper">
							<div class="label-wrapper">
								<label for="collectibleDepth"> <?php echo __('Depth (inches)')
									?></label>
							</div>
							<?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end();?>
				<?php echo $this -> Form -> create('Collectible', array('url' => '/admin/collectibles/view/' . $currentCollectibleId, 'id' => 'skip-manufacture-form'));?>
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
<div id="edit-series-dialog" class="dialog" title="Category">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Select from the categories below to change.  Some categories might have sub-categories you can choose from.');
					?>
				</div>
			</div>
			<div class="component-view">
				<fieldset>
					<ul id="edit-series-dialog-fields" class="form-fields"></ul>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<div id="edit-collectibletype-dialog" class="dialog" title="Edit Collectible Type">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Select from the types below to change.  Some types might have sub-types you can choose from.')
					?>
				</div>
			</div>
			<div class="component-view">
				<fieldset>
					<ul id="edit-collectibletype-dialog-fields" class="form-fields"></ul>
				</fieldset>
			</div>
		</div>
	</div>
</div>