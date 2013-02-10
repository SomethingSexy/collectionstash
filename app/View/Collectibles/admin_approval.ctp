<div id="admin-edit" class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page collectibles-approval">
			<div class="title">
				<h2><?php echo __('Approval');?></h2>
			</div>
			<?php echo $this -> element('flash');?>

			<div class="collectible item">
				<div class="collectible detail-wrapper">
					<div class="collectible detail">
						<div class="detail title">
							<h3><?php echo __('Manufacture Details');?></h3>
						</div>
						<dl>
							<dt>
								<?php echo __('Manufacture');?>
							</dt>
							<?php
							if (isset($collectible['Collectible']['manufacture_id_changed']) && $collectible['Collectible']['manufacture_id_changed']) {
								echo '<dd class="changed">';
							} else {
								echo '<dd>';
							}
							?>
							<a href="<?php echo '/manufactures/view/' . $collectible['Manufacture']['id'];?>"> <?php echo $collectible['Manufacture']['title'];?></a>
							</dd> <?php

							//If the value is set and not empty OR if something changed
							if (isset($collectible['Collectible']['series_id']) && !empty($collectible['Collectible']['series_id']) || (isset($collectible['Collectible']['series_id_changed']) && $collectible['Collectible']['series_id_changed'])) {
								echo '<dt>';
								echo __('Category');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['series_id_changed']) && $collectible['Collectible']['series_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['series_id'])) {
									echo $collectible['Collectible']['seriesPath'];
								} else {
									echo __('Removed');
								}

								echo '</dd>';
							}

							//If the value is set and not empty OR if something changed
							if (isset($collectible['Collectible']['license_id']) && !empty($collectible['Collectible']['license_id']) || (isset($collectible['Collectible']['license_id_changed']) && $collectible['Collectible']['license_id_changed'])) {
								echo '<dt>';
								echo __('Brand');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['license_id_changed']) && $collectible['Collectible']['license_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['license_id'])) {
									echo $collectible['License']['name'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['collectibletype_id']) && !empty($collectible['Collectible']['collectibletype_id']) || (isset($collectible['Collectible']['collectibletype_id_changed']) && $collectible['Collectible']['collectibletype_id_changed'])) {
								echo '<dt>';
								echo __('Type');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['collectibletype_id_changed']) && $collectible['Collectible']['collectibletype_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['collectibletype_id'])) {
									echo $collectible['Collectibletype']['name'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['specialized_type_id']) && !empty($collectible['Collectible']['specialized_type_id']) || (isset($collectible['Collectible']['specialized_type_id_changed']) && $collectible['Collectible']['specialized_type_id_changed'])) {
								echo '<dt>';
								echo __('Manufacturer Type');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['specialized_type_id_changed']) && $collectible['Collectible']['specialized_type_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['specialized_type_id'])) {
									echo $collectible['SpecializedType']['name'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['name']) && !empty($collectible['Collectible']['name']) || (isset($collectible['Collectible']['name_changed']) && $collectible['Collectible']['name_changed'])) {
								echo '<dt>';
								echo __('Name');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['name_changed']) && $collectible['Collectible']['name_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['name'])) {
									echo $collectible['Collectible']['name'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['variant']) && !empty($collectible['Collectible']['variant']) || (isset($collectible['Collectible']['variant_changed']) && $collectible['Collectible']['variant_changed'])) {
								echo '<dt>';
								echo __('Variant');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['variant_changed']) && $collectible['Collectible']['variant_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if ($collectible['Collectible']['variant']) {
									echo __('Yes');
								} else {
									echo __('No');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['scale_id']) && !empty($collectible['Collectible']['scale_id']) || (isset($collectible['Collectible']['scale_id_changed']) && $collectible['Collectible']['scale_id_changed'])) {
								echo '<dt>';
								echo __('Scale');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['scale_id_changed']) && $collectible['Collectible']['scale_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['scale_id'])) {
									echo $collectible['Scale']['scale'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['release']) && !empty($collectible['Collectible']['release']) || (isset($collectible['Collectible']['release_changed']) && $collectible['Collectible']['release_changed'])) {
								echo '<dt>';
								echo __('Release Year');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['release_changed']) && $collectible['Collectible']['release_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['release'])) {
									echo $collectible['Collectible']['release'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['description']) && !empty($collectible['Collectible']['description']) || (isset($collectible['Collectible']['description_changed']) && $collectible['Collectible']['description_changed'])) {
								echo '<dt>';
								echo __('Description');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['description_changed']) && $collectible['Collectible']['description_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['description'])) {
									echo $collectible['Collectible']['description'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['code']) && !empty($collectible['Collectible']['code']) || (isset($collectible['Collectible']['code_changed']) && $collectible['Collectible']['code_changed'])) {
								echo '<dt>';
								echo __('Product code');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['code_changed']) && $collectible['Collectible']['code_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['code'])) {
									echo $collectible['Collectible']['code'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['upc']) && !empty($collectible['Collectible']['upc']) || (isset($collectible['Collectible']['upc_changed']) && $collectible['Collectible']['upc_changed'])) {
								echo '<dt>';
								echo __('Product UPC');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['upc_changed']) && $collectible['Collectible']['upc_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['upc'])) {
									echo $collectible['Collectible']['upc'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['msrp']) && !empty($collectible['Collectible']['msrp']) || (isset($collectible['Collectible']['msrp_changed']) && $collectible['Collectible']['msrp_changed'])) {
								echo '<dt>';
								echo __('Original Retail Price');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['msrp_changed']) && $collectible['Collectible']['msrp_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['msrp'])) {
									echo $collectible['Collectible']['msrp'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}
                            

                            if (isset($collectible['Collectible']['currency_id']) && !empty($collectible['Collectible']['currency_id']) || (isset($collectible['Collectible']['currency_id_changed']) && $collectible['Collectible']['currency_id_changed'])) {
                                echo '<dt>';
                                echo __('Currency');
                                echo '</dt>';

                                //Check if it is changed first
                                if (isset($collectible['Collectible']['currency_id_changed']) && $collectible['Collectible']['currency_id_changed']) {
                                    echo '<dd class="changed">';
                                } else {
                                    echo '<dd>';
                                }

                                if (!empty($collectible['Collectible']['currency_id'])) {
                                    echo $collectible['Currency']['iso_code'];
                                } else {
                                    echo __('Removed');
                                }
                                echo '</dd>';
                            }                            

							if (isset($collectible['Collectible']['limited']) && !empty($collectible['Collectible']['limited']) || (isset($collectible['Collectible']['limited_changed']) && $collectible['Collectible']['limited_changed'])) {
								echo '<dt>';
								echo __('Limited Edition');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['limited_changed']) && $collectible['Collectible']['limited_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if ($collectible['Collectible']['limited']) {
									echo __('Yes');
								} else {
									echo __('No');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['edition_size']) && !empty($collectible['Collectible']['edition_size']) || (isset($collectible['Collectible']['edition_size_changed']) && $collectible['Collectible']['edition_size_changed'])) {
								echo '<dt>';
								echo __('Edition Size');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['edition_size_changed']) && $collectible['Collectible']['edition_size_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['edition_size'])) {
									echo $collectible['Collectible']['edition_size'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['numbered']) && !empty($collectible['Collectible']['numbered']) || (isset($collectible['Collectible']['numbered_changed']) && $collectible['Collectible']['numbered_changed'])) {
								echo '<dt>';
								echo __('Numbered');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['numbered_changed']) && $collectible['Collectible']['numbered_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if ($collectible['Collectible']['numbered']) {
									echo __('Yes');
								} else {
									echo __('No');
								}
								echo '</dd>';
							}
							
							if (isset($collectible['Collectible']['signed']) && !empty($collectible['Collectible']['signed']) || (isset($collectible['Collectible']['signed_changed']) && $collectible['Collectible']['signed_changed'])) {
								echo '<dt>';
								echo __('Signed');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['signed_changed']) && $collectible['Collectible']['signed_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if ($collectible['Collectible']['signed']) {
									echo __('Yes');
								} else {
									echo __('No');
								}
								echo '</dd>';
							}							
							
							if (isset($collectible['Collectible']['exclusive']) && !empty($collectible['Collectible']['exclusive']) || (isset($collectible['Collectible']['exclusive_changed']) && $collectible['Collectible']['exclusive_changed'])) {
								echo '<dt>';
								echo __('Exclusive');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['exclusive_changed']) && $collectible['Collectible']['exclusive_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if ($collectible['Collectible']['exclusive']) {
									echo __('Yes');
								} else {
									echo __('No');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['retailer_id']) && !empty($collectible['Collectible']['retailer_id']) || (isset($collectible['Collectible']['retailer_id_changed']) && $collectible['Collectible']['retailer_id_changed'])) {
								echo '<dt>';
								echo __('Exclusive Retailer');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['retailer_id_changed']) && $collectible['Collectible']['retailer_id_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['retailer_id'])) {
									echo $collectible['Retailer']['name'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}
							
							if (isset($collectible['Collectible']['pieces']) && !empty($collectible['Collectible']['pieces']) || (isset($collectible['Collectible']['pieces_changed']) && $collectible['Collectible']['pieces_changed'])) {
								echo '<dt>';
								echo __('Pieces');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['pieces_changed']) && $collectible['Collectible']['pieces_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['pieces'])) {
									echo $collectible['Collectible']['pieces'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}							
							
							if (isset($collectible['Collectible']['product_weight']) && !empty($collectible['Collectible']['product_weight']) || (isset($collectible['Collectible']['product_weight_changed']) && $collectible['Collectible']['product_weight_changed'])) {
								echo '<dt>';
								echo __('Weight');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['product_weight_changed']) && $collectible['Collectible']['product_weight_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['product_weight'])) {
									echo $collectible['Collectible']['product_weight'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}

							if (isset($collectible['Collectible']['product_length']) && !empty($collectible['Collectible']['product_length']) || (isset($collectible['Collectible']['product_length_changed']) && $collectible['Collectible']['product_length_changed'])) {
								echo '<dt>';
								echo __('Height');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['product_length_changed']) && $collectible['Collectible']['product_length_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['product_length'])) {
									echo $collectible['Collectible']['product_length'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}						
						
							if (isset($collectible['Collectible']['product_width']) && !empty($collectible['Collectible']['product_width']) || (isset($collectible['Collectible']['product_width_changed']) && $collectible['Collectible']['product_width_changed'])) {
								echo '<dt>';
								echo __('Width');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['product_width_changed']) && $collectible['Collectible']['product_width_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['product_width'])) {
									echo $collectible['Collectible']['product_width'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}							

							if (isset($collectible['Collectible']['product_depth']) && !empty($collectible['Collectible']['product_depth']) || (isset($collectible['Collectible']['product_depth_changed']) && $collectible['Collectible']['product_depth_changed'])) {
								echo '<dt>';
								echo __('Depth');
								echo '</dt>';

								//Check if it is changed first
								if (isset($collectible['Collectible']['product_depth_changed']) && $collectible['Collectible']['product_depth_changed']) {
									echo '<dd class="changed">';
								} else {
									echo '<dd>';
								}

								if (!empty($collectible['Collectible']['product_depth'])) {
									echo $collectible['Collectible']['product_depth'];
								} else {
									echo __('Removed');
								}
								echo '</dd>';
							}							
						
							?>
						</dl>
					</div>
				</div>
			</div>
			<?php echo $this -> Form -> create('Approval', array('url' => '/admin/edits/approval_2/' . $editId, 'id' => 'approval-form'));?>
			<input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
			<fieldset>
				<ul class="form-fields unstyled">
					<li>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Notes')
								?></label>
						</div>
						<textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
					</li>
				</ul>
			</fieldset>
			</form>
			<div class="links">
				<button id="approval-button" class="btn btn-primary"><?php echo __('Approve');?></button>
				<button id="deny-button" class="btn"><?php echo __('Deny');?></button>
			</div>
			<script>
				//Eh move this out of here
				$('#approval-button').click(function() {
					$('#approve-input').val('true');
					$('#approval-form').submit();
				});
				$('#deny-button').click(function() {
					$('#approve-input').val('false');
					$('#approval-form').submit();
				});

			</script>
		</div>
	</div>
</div>
