<?php if (isset($collectibleDetail['CollectiblePriceFact'])) { ?>

<div class="panel panel-default stacked">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="icon-dollar"></i> Price Guide</h3>
	</div>
	<div class="panel-body">
		<div class="price-guide">
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price']; ?></span>
				Average price from <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions'] == 1) {
					echo __('transaction');
				} else {
					echo __('transactions');
				}
			?>
			</div>
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price_ebay']; ?></span>
				Average eBay price <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions_ebay'] == 1) {
					echo __('transaction');
				} else {
					echo __('transactions');
				}
			?>
				
			</div>
			<div class="average">
				<span class="average-value">$<?php echo $collectibleDetail['CollectiblePriceFact']['average_price_external']; ?></span>
				Average eBay price <?php echo $collectibleDetail['CollectiblePriceFact']['total_transactions_external']; ?><?php
				if ($collectibleDetail['CollectiblePriceFact']['total_transactions_external'] == 1) {
					echo __('transaction');
				} else {
					echo __('transactions');
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
		{@if cond=" ('{errors}'.length === 0) "}
		
		{:else}
			<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>Oops! Something went wrong.</h4>
				<ul>
				{#errors}
					{#message}
					<li>{.}</li>
					{/message}
				
				{/errors}
				</ul>
			</div>
		{/if}
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
		<?php if(empty($collectibleDetail['Listing'])){ ?>
			<p><?php echo __('No listings have been added.'); ?></p>
		<?php } else {
				$activeListings = false;
				$completedTransactions = false;
				$unsoldListings = false;
				foreach ($collectibleDetail['Listing'] as $key => $value) {
				if (!$value['processed']) {
				$activeListings = true;
				} else if ($value['status'] === 'completed' && $value['quantity_sold'] === '0') {
				$unsoldListings = true;
				}

				if (count($value['Transaction']) > 0) {
				$completedTransactions = true;
				}

				}
			?>

		 	<h4>Active Listings</h4>
		 	<?php if($activeListings){ ?>
		 	<div class="table-responsive">
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
							 <?php if(!$value['processed']){ ?>
								<tr>
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

												echo '"><i class=" icon-exclamation-sign"></i></a>';
											}
										?>
										<?php if($allowDeleteListing){?>
											<a data-id="<?php echo  $value['id'];?>" class="btn btn-default delete" href="#" title="Delete">
												<i class="icon-remove"></i>
											</a>
										<?php }?>
										</div>
									</td>
								</tr>
							<?php } ?>						
						<?php } ?>
					</tbody>
				</table> 	
			</div>
		 	<?php } else { ?>
		 		<p><?php echo __('There are no active listings.'); ?></p>
		 	<?php } ?>
		 	
		 	<h4>Unsold Listings</h4>
		 	{@eq key="{unsoldListings}" value="true" type="boolean"}
		 	<div class="table-responsive">
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
						{#listings allowMaintenance=allowMaintenance allowAdd=allowAdd}
							{@if cond=" ('{processed}' && '{status}' === 'completed' && '{quantity_sold}' === '0') "}
								<tr>
									<td>{type}</td>
									<td><a target="_blank" href="{url}">{listing_name}</a></td>
									<td>{listing_price}</td>
									<td>{condition_name}</td>
									<td>{start_date}</td>
									<td>{end_date}</td>
									<td>
										<div class="btn-group actions">
											{@eq key="{allowAdd}" value="true" type="boolean"}
											<a data-id="{id}" class="btn btn-default flag  {@if cond=" ( '{flagged}' && !'{allowMaintenance}' ) "}disabled{/if} {@eq key="{flagged}" value="true" type="boolean"}btn-danger{/eq}" href="#" title="{@eq key="{flagged}" value="true" type="boolean"}This listing has been flagged already. {:else}Flag if you think there is an error.{/eq}">
												<i class=" icon-exclamation-sign"></i>
											</a>
											{/eq}
											{@eq key="{allowMaintenance}" value="true" type="boolean"}
												<a data-id="{id}" class="btn btn-default delete" href="#" title="Delete">
													<i class="icon-remove"></i>
												</a>
											{/eq}
										</div>
									</td>
								</tr>
							{/if}
						{/listings}
					</tbody>
				</table> 
			</div>	
		 	{:else}
		 		<p>There are no unsold listings.</p>
		 	{/eq}
			
			<h4>Completed Transactions</h4>
			{@eq key="{completedTransactions}" value="true" type="boolean"}
			<div class="table-responsive">
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
						{#listings allowMaintenance=allowMaintenance allowAdd=allowAdd}
							{#Transaction allowDeleteListing=allowDeleteListing type=type itemId=ext_item_id listing_name=listing_name flagged=flagged allowAdd=allowAdd listing_type_id=listing_type_id}
							<tr>
								<td>
									{@if cond="( '{listing_type_id}' === '2' ||  '{listing_type_id}' === '3' )"}
										{@eq key="{listing_type_id}" value="2" type="string"}
											User Sale
										{/eq}
										{@eq key="{listing_type_id}" value="3" type="string"}
											User Trade
										{/eq}
									{:else}
										{type}
									{/if}
								</td>
								<td>
									{@if cond="( '{listing_type_id}' === '2' ||  '{listing_type_id}' === '3' )"}
										{listing_name}
									{:else}
										<a target="_blank" href="{url}">{listing_name}</a>
									{/if}							
								</td>
								<td>
										{@eq key="{listing_type_id}" value="1" type="string"}
											{sale_price}
										{/eq}
										{@eq key="{listing_type_id}" value="2" type="string"}
											{sale_price}
										{/eq}
										{@eq key="{listing_type_id}" value="3" type="string"}
											{traded_for}
										{/eq}
								</td>
								<td>{condition_name}</td>
								<td>{#sale_date} {.} {:else} Missing {/sale_date}</td>
								<td>
									<div class="btn-group actions">
										{@eq key="{allowAdd}" value="true" type="boolean"}
											<a data-id="{listing_id}" class="btn btn-default flag {@if cond=" ( '{flagged}' && !'{allowMaintenance}' ) "}disabled{/if} {@eq key="{flagged}" value="true" type="boolean"}btn-danger{/eq}" href="#" title="{@eq key="{flagged}" value="true" type="boolean"}This listing has been flagged already. {:else}Flag if you think there is an error.{/eq}">
												<i class=" icon-exclamation-sign"></i>
											</a>
										{/eq}
										{@eq key="{allowMaintenance}" value="true" type="boolean"}
											<a data-id="{listing_id}" class="btn btn-default delete" href="#" title="Delete">
												<i class="icon-remove"></i>
											</a>
										{/eq}
									</div>
								</td>
							</tr>
							{/Transaction}
						{/listings}
					</tbody>
				</table>	
			</div>
			{:else}
		 		<p>There are no completed transactions.</p>
		 	{/eq}			
			
		<?php } ?>
		
	</div>
</div>