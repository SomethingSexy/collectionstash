<?php if (isset($collectibleDetail['CollectiblePriceFact'])) { ?>

<div class="panel panel-default stacked">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-dollar"></i> Price Guide</h3>
	</div>
	<div class="panel-body">
		<div class="price-guide">
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price']; ?></span>
				Average price from <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions'] == 1) {
					echo __(' transaction');
				} else {
					echo __(' transactions');
				}
			?>
			</div>
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price_ebay']; ?></span>
				Average eBay price <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions_ebay'] == 1) {
					echo __(' transaction');
				} else {
					echo __(' transactions');
				}
			?>
				
			</div>
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price_external']; ?></span>
				Average external listing price <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions_external']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions_external'] == 1) {
					echo __(' transaction');
				} else {
					echo __(' transactions');
				}
			?>	
			</div>			
		</div>	
		<p class="pull-right">Average prices update once a day.</p>
		
	</div>
	
</div>
<?php } ?>




<div class="panel panel-default action-table">
	<div class="panel-heading">
		<h3 class="panel-title">Listings and Transaction History</h3>
	</div>
	<div class="panel-body">	
		<?php if($allowAddListing){ ?>
		<div class="form-group clearfix">
			<div class="input-group col-lg-6">
				<input type="text" value="" name="item" placeholder="Item Number" id="inputListingItem"  class="form-control" autocomplete="off">
				<span class="input-group-btn">
					<button class="btn disabled" disabled="disabled">
						eBay
					</button>
					<button type="button" class="btn add-transaction">
						Add Listing
					</button>
				</div>
		
			</div><span class="help-block inline-error"></span>
		
		<?php } ?>
		<?php 
	
		if(empty($collectibleDetail['Listing'])){ ?>
			<p class="no-transactions"><?php echo __('No listings have been added.'); ?></p>
		<?php } ?>
		
		<?php 
				$activeListings = false;
				$completedTransactions = false;
				$unsoldListings = false;
				
				$activeListingCount = 0;
				$completedTransactionsCount = 0;
				$unsoldListingsCount = 0;
				
				foreach ($collectibleDetail['Listing'] as $key => $value) {
					if (!$value['processed']) {
						$activeListings = true;
						$activeListingCount = $activeListingCount + 1;
					} else if ($value['status'] === 'completed' && $value['quantity_sold'] === '0') {
						$unsoldListings = true;
						$unsoldListingsCount = $unsoldListingsCount + 1;
					}
					if (count($value['Transaction']) > 0) {
						$completedTransactions = true;
						$completedTransactionsCount = $completedTransactionsCount + count($value['Transaction']);
					}
				}
			?>
			
			<div class="all-transactions" <?php if(empty($collectibleDetail['Listing'])){ echo 'style="display: none"';} ?>>
		 	<h4>Active Listings (<span class="active-listings-count"><?php echo $activeListingCount;?></span>)</h4>
		 	<button type="button" class="btn btn-default btn-collapser collapsed btn-active-listings" <?php if(!$activeListings){ echo 'style="display:none"';} ?> data-toggle="collapse" data-target=".active-listings">
				<i class="fa fa-caret-square-o-right"></i>
			</button>	
		 	<div class="table-responsive active-listings collapse spacer">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Type</th>
							<th>Listing name</th>
							<th>Current Price</th>
							<th>Condition</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th> </th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($collectibleDetail['Listing'] as $key => $value) { ?>
							 <?php if(!$value['processed']){ 
							 	
								$listingJSON = json_encode($value);
								$listingJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $listingJSON));
								?>
								<tr class="listing" data-listing="<?php echo $listingJSON?>">
									<td>
										<?php
										if ($value['listing_type_id'] === '2') {
											echo 'User Sale';
										} else if ($value['listing_type_id'] === '3') {
											echo 'User Trade';
										} else {
											echo $value['type'];
										}
										?>
									</td>
									<td>
										<?php
										if ($value['listing_type_id'] === '2' || $value['listing_type_id'] === '3') {
											echo $value['listing_name'];
										} else {
											echo '<a target="_blank" href="' . $value['url'] . '">' . $value['listing_name'] . '</a>';
										}
										?>
									</td>
									<td>
										<?php
										if ($value['listing_type_id'] === '1') {
											echo '$' . $value['current_price'];
										} else if ($value['listing_type_id'] === '2') {
											echo '$' . $value['current_price'];
										} else if ($value['listing_type_id'] === '3') {
											echo $value['traded_for'];
										}
										?>
									</td>
									<td><?php echo $value['condition_name']; ?></td>
									<td><?php echo $value['start_date']; ?></td>
									<td><?php echo $value['end_date']; ?></td>
									<td>
										<div class="btn-group actions">
											<?php
											if ($allowAddListing) {
												echo '<a data-id="' . $value['id'] . '" class="btn btn-default flag';
												if ($value['flagged'] && !$allowDeleteListing) {
													echo ' disabled';
												}

												if ($value['flagged']) {
													echo ' btn-danger';
												}

												echo '"  href="#" title="';
												// end class

												if ($value['flagged']) {
													echo __('This listing has been flagged already.');
												} else {
													echo __('Flag if you think there is an error.');
												}

												echo '"><i class="fa fa-exclamation-circle"></i></a>';
											}
										?>
										<?php if($allowDeleteListing){?>
											<a data-id="<?php echo $value['id']; ?>" class="btn btn-default delete" href="#" title="Delete">
												<i class="fa fa-times-circle"></i>
											</a>
										<?php } ?>
										</div>
									</td>
								</tr>
							<?php } ?>						
						<?php } ?>
					</tbody>
				</table> 	
			</div>
		 	<?php if(!$activeListings){ ?>
		 		<p class="no-active-listings"><?php echo __('There are no active listings.'); ?></p>
		 	<?php } ?>
		 	
		 	<h4>Unsold Listings (<span class="unsold-listings-count"><?php echo $unsoldListingsCount;?></span>)</h4>	 
			<button type="button" class="btn btn-default btn-collapser collapsed btn-unsold-listings" <?php if(!$unsoldListings){ echo 'style="display:none"';} ?> data-toggle="collapse" data-target=".unsold-listings">
				<i class="fa fa-caret-square-o-right"></i>
			</button>	
		 	<div class="table-responsive unsold-listings collapse spacer">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Type</th>
							<th>Listing name</th>
							<th>Listing Price</th>
							<th>Condition</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th> </th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($collectibleDetail['Listing'] as $key => $value) { ?>
							<?php if($value['processed'] && $value['status'] === 'completed' && $value['quantity_sold'] === '0'){ 
								$listingJSON = json_encode($value);
								$listingJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $listingJSON));
								?>
								<tr class="listing" data-listing="<?php echo $listingJSON?>">
									<td><?php echo $value['type']; ?></td>
									<td><a target="_blank" href="{url}"><?php echo $value['listing_name']; ?></a></td>
									<td><?php echo $value['listing_price']; ?></td>
									<td><?php echo $value['condition_name']; ?></td>
									<td><?php echo $value['start_date']; ?></td>
									<td><?php echo $value['end_date']; ?></td>
									<td>
										<div class="btn-group actions">
											<?php
											if ($allowAddListing) {
												echo '<a data-id="' . $value['id'] . '" class="btn btn-default flag';
												if ($value['flagged'] && !$allowDeleteListing) {
													echo ' disabled';
												}

												if ($value['flagged']) {
													echo ' btn-danger';
												}

												echo '"  href="#" title="';
												// end class

												if ($value['flagged']) {
													echo __('This listing has been flagged already.');
												} else {
													echo __('Flag if you think there is an error.');
												}

												echo '"><i class=" fa fa-exclamation-circle"></i></a>';
											}
										?>
										<?php if($allowDeleteListing){?>
											<a data-id="<?php echo $value['id']; ?>" class="btn btn-default delete" href="#" title="Delete">
												<i class="fa fa-times-circle"></i>
											</a>
										<?php } ?>
										</div>
									</td>
								</tr>
							<?php } ?>						
						<?php } ?>
					</tbody>
				</table> 
			</div>	
		 	<?php if(!$unsoldListings){ ?>
		 		<p class="no-unsold-listings"><?php echo __('There are no unsold listings.'); ?></p>
		 	<?php } ?>
		 	
			
			<h4>Completed Listings (<span class="completed-listings-count"><?php echo $completedTransactionsCount;?></span>)</h4>
			<button type="button" class="btn btn-default btn-collapser collapsed btn-completed-listings" <?php if(!$completedTransactions){ echo 'style="display:none"';} ?> data-toggle="collapse" data-target=".completed-listings">
				<i class="fa fa-caret-square-o-right"></i>
			</button>
			<div class="table-responsive completed-listings collapse spacer">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Type</th>
							<th>Listing name</th>
							<th>Sale Price</th>
							<th>Condition</th>
							<th>Date</th>
							<th> </th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($collectibleDetail['Listing'] as $key => $value) { ?>
							<?php foreach ($value['Transaction'] as $key => $transaction) { 
								$listingJSON = json_encode($value);
								$listingJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $listingJSON));
								?>
							<tr class="listing" data-listing="<?php echo $listingJSON?>">
								<td>
									<?php
									if ($value['listing_type_id'] === '2') {
										echo 'User Sale';
									} else if ($value['listing_type_id'] === '3') {
										echo 'User Trade';
									} else {
										echo $value['type'];
									}
									?>
								</td>
								<td>
									<?php
									if ($value['listing_type_id'] === '2' || $value['listing_type_id'] === '3') {
										echo $value['listing_name'];
									} else {
										echo '<a target="_blank" href="' . $value['url'] . '">' . $value['listing_name'] . '</a>';
									}
									?>						
								</td>
								<td>
									<?php
									if ($value['listing_type_id'] === '1') {
										echo '$' . $transaction['sale_price'];
									} else if ($value['listing_type_id'] === '2') {
										echo '$' . $transaction['sale_price'];
									} else if ($value['listing_type_id'] === '3') {
										echo $transaction['traded_for'];
									}
									?>
								</td>
								<td><?php echo $value['condition_name']; ?></td>
								<td>
									<?php
									if ($transaction['sale_date']) {
										echo $transaction['sale_date'];
									} else {
										echo 'Missing';
									}
								?>
								</td>
								<td>
									<div class="btn-group actions">
										<?php
										if ($allowAddListing) {
											echo '<a data-id="' . $value['id'] . '" class="btn btn-default flag';
											if ($value['flagged'] && !$allowDeleteListing) {
												echo ' disabled';
											}

											if ($value['flagged']) {
												echo ' btn-danger';
											}

											echo '"  href="#" title="';
											// end class

											if ($value['flagged']) {
												echo __('This listing has been flagged already.');
											} else {
												echo __('Flag if you think there is an error.');
											}

											echo '"><i class=" fa fa-exclamation"></i></a>';
										}
										?>
										<?php if($allowDeleteListing){?>
											<a data-id="<?php echo $value['id']; ?>" class="btn btn-default delete" href="#" title="Delete">
												<i class="fa fa-times-circle"></i>
											</a>
										<?php } ?>
									</div>
								</td>
							</tr>
							<?php } ?>						
						<?php } ?>
					</tbody>
				</table>	
			</div>
		 	<?php if(!$completedTransactions){ ?>
		 		<p class="no-completed-listings"><?php echo __('There are no completed listings.'); ?></p>
		 	<?php } ?>		
		</div>	
	</div>
</div>