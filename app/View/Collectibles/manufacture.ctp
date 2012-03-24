<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this -> Wizard -> progressMenu(array('manufacture' => 'Manufacturer Details', 'attributes' => 'Accessories/Features', 'tags' => 'Tags', 'image' => 'Image', 'review' => 'Review'));?>
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Add New Collectible');?></h2>
			<?php if($this -> Session -> check('add.collectible.variant')) {
			?>
			<div class="actions">
				<ul>
					<li>
						<a id="base-collectible-link" class="link"><?php echo __('View Base Collectible');?></a>
					</li>
				</ul>
			</div>
			<?php }?>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Fill out the following information to add the collectible.
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('url' => '/' . $this -> params['controller'] . '/' . $this -> action . '/manufacture', 'type' => 'file'));?>
				<fieldset>
					<legend>
						<?php echo __('Manufacturer Details');?>
					</legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for=""> <?php echo __('Manufacturer')
									?></label>
							</div>
							<div class="static-field">
								<?php echo $manufacturer['Manufacture']['title'];?>
								<?php echo '<input type="hidden" id="CollectibleManufactureId" value="' . $manufacturer['Manufacture']['id'] . '" />';?>
							</div>
						</li>
						<li>
							<div class="label-wrapper">
								<label for=""> <?php echo __('Collectible Type')
									?></label>
							</div>
							<div class="static-field">
								<?php echo $collectibleType['Collectibletype']['name']
								?>
							</div>
						</li>
						</li> <?php
                        if (!isset($specializedTypes)) {
                            $specializedTypes = array();
                        }
						?>
						<?php
                        if (!$this -> Session -> check('add.collectible.variant')) {
                            if (empty($specializedTypes)) {
                                echo '<li class="hidden">';
                            } else {
                                echo '<li>';
                            }

                            echo $this -> Form -> input('specialized_type_id', array('empty' => true, 'label' => __('Manufacturer Collectible Type'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));
                            echo '</li>';
                        } else {
                            //If it is a variant, see if we have one added first
                            if (isset($collectible['Collectible']['specialized_type_id']) && !empty($collectible['Collectible']['specialized_type_id'])) {
                                echo '<li>';
                                echo '<div class="label-wrapper">';
                                echo '<label for="">';
                                echo __('Manufacturer Collectible Type');
                                echo '</label></div>';
                                echo '<div class="static-field">';
                                echo $collectible['SpecializedType']['name'];
                                echo $this -> Form -> hidden('specialized_type_id');
                                echo '</div>';
                                echo '</li>';
                            }
                        }
						?>

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
                        if (!$this -> Session -> check('add.collectible.variant')) {
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
                        } else {
                            echo '<div class="static-field">';
                            echo $collectible['Series']['name'];
                            echo $this -> Form -> hidden('series_id');
                            echo '</div>';
                        }
						?>
						</li>
						<li>
							<?php echo $this -> Form -> input('name', array('escape' => false, 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<?php
                        if (empty($licenses)) {
                            echo '<li class="hidden">';
                        } else {
                            echo '<li>';
                        }
						?>
						<?php
                        if (!$this -> Session -> check('add.collectible.variant')) {
                            echo $this -> Form -> input('license_id', array('label' => __('Brand'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));
                        } else {
                            echo '<div class="label-wrapper">';
                            echo '<label for="">';
                            echo __('Brand');
                            echo '</label></div>';
                            echo '<div class="static-field">';
                            echo $collectible['License']['name'];
                            echo $this -> Form -> hidden('license_id');
                            echo '</div>';
                        }
						?>
						</li>

						<?php
                        if (!$this -> Session -> check('add.collectible.variant')) {
                            echo '<li>';
                            echo $this -> Form -> input('scale_id', array('empty' => true, 'label' => __('Scale'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));
                            echo '</li>';
                        } else {
                            if (isset($collectible['Collectible']['scale_id']) && !empty($collectible['Collectible']['scale_id'])) {
                                echo '<li>';    
                                echo '<div class="label-wrapper">';
                                echo '<label for="">';
                                echo __('Scale');
                                echo '</label></div>';
                                echo '<div class="static-field">';
                                echo $collectible['Scale']['scale'];
                                echo $this -> Form -> hidden('scale_id');
                                echo '</div>';
                                echo '</li>';
                            }

                        }
						?>
						</li>
						<li>
							<?php
                            $data = '';
                            if (isset($this -> request -> data['Collectible']['description'])) {
                                $data = str_replace('\n', "\n", $this -> request -> data['Collectible']['description']);
                                $data = str_replace('\r', "\r", $data);
                            }

                            echo $this -> Form -> input('description', array('escape' => false, 'label' => __('Description'), 'before' => '<div class="label-wrapper">', 'between' => '</div>', 'value' => $data));
							?>
						</li>
                        <li class="msrp">
                            <?php echo $this -> Form -> input('msrp', array('maxLength'=> 10000, 'label'=>__('Original Retail Price'),'before' => '<div class="label-wrapper">','between'=>'</div>', 'after'=> $this -> Form -> input('currency_id', array('label' => false, 'div' => 'currency'))));?>
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
							<?php echo $this -> Form -> input('url', array('escape' => false, 'label' => __('URL'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('limited', array('label' => __('Limited Edition'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<?php
                        if (isset($this -> request -> data['Collectible']['limited']) && $this -> request -> data['Collectible']['limited']) {
                            echo '<li>';
                        } else {
                            echo '<li class="hidden">';
                        }
						?>
						<?php echo $this -> Form -> input('edition_size', array('label' => __('Edition Size'), 'before' => '<div class="label-wrapper">', 'between' => '</div><a class="ui-icon ui-icon-info" title="' . __('This is the edition size of the collectible.  If it is unknown or it does not have a specific edition size, leave blank.', true) . '" alt="info"></a>'));?>
						</li>
						<?php
                        if (isset($this -> request -> data['Collectible']['limited']) && $this -> request -> data['Collectible']['limited']) {
                            echo '<li>';
                        } else {
                            echo '<li class="hidden">';
                        }
						?>
						<?php echo $this -> Form -> input('numbered', array('label' => __('Numbered'), 'before' => '<div class="label-wrapper">', 'between' => '</div>', 'after' => '<a class="ui-icon ui-icon-info" title="' . __('A collectible is considered numbered if it has an edition size and is indivudually numbered.', true) . '" alt="info"></a>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('exclusive', array('label' => __('Exclusive'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('retailer_id', array('label' => __('Exclusive Retailer'), 'before' => '<div class="label-wrapper">', 'between' => '</div>', 'empty' => true));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('code', array('label' => __('Product Code'), 'before' => '<div class="label-wrapper">', 'between' => '</div><a class="ui-icon ui-icon-info" title="' . __('This is the item code or product code given by the manufacture.', true) . '" alt="info"></a>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('upc', array('label' => __('Product UPC'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('pieces', array('label' => __('Number of Pieces'), 'before' => '<div class="label-wrapper">', 'between' => '</div><a class="ui-icon ui-icon-info" title="' . __('This is the number of pieces that come with this collectible.  If unknown, please leave blank.', true) . '" alt="info"></a>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('product_weight', array('label' => __('Weight (lbs)'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('product_length', array('label' => __('Height (inches)'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('product_width', array('label' => __('Width (inches)'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('product_depth', array('label' => __('Depth (inches)'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>
<div id="edit-series-dialog" class="dialog" title="Category">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Select from the categories below to change.  Some categories might have sub-categories you can choose from.');?>
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
<?php if($this -> Session -> check('add.collectible.variant')) {
?>
<div id="base-collectible" class="dialog">
	<?php
    echo $this -> element('collectible_detail_core', array('showEdit' => false, 'showImage' => false, 'showAttributes' => false, 'collectibleCore' => $collectible));
	?>
</div>
<?php }?>
<script>
	$(function() {
		$(".ui-icon-info").tooltip({
			position : 'center right',
			opacity : 0.7
		});
		$('#base-collectible').dialog({
			'autoOpen' : false,
			'width' : 750,
			'height' : 'auto',
			'resizable' : false,
			'modal' : true
		});
		$('#base-collectible-link').click(function() {
			$('#base-collectible').dialog('open');
		});
	});

</script>