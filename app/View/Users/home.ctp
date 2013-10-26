<div class="col-md-8 home">
		<?php
	if ($user['User']['collectibles_user_count'] == 0) {
	?>
	<div class="row spacer">
		<div class="col-md-12">
			<div class="hero-unit">
				<h1>Getting started with Collection Stash!</h1>
				<p>
					You can now enjoy all of the benefits of being a registered membered.  Get started by searching for collectibles you own and adding them to your stash or helping catalog collectibles by submitting new ones.
				</p>
				<p>
					<a target="_blank" href="/pages/collection_stash_documentation" class="btn btn-primary btn-large"> Learn more </a>
				</p>
			</div>

		</div>
	</div>
	<?php } ?>
	<div class="row spacer">
		<div class="col-md-12">
			<div class="panel panel-default stacked">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="icon-dollar"></i> Stash Value Breakdown</h3>
					
				</div>
				<div class="panel-body">
					<div class="stash-value-guide">
					<?php
					foreach ($stashes as $key => $value) {
						if ($value['Stash']['name'] === 'Default') {
							echo '<div class="average">';
							echo '<span class="average-value">$' . $value['StashFact']['msrp_value'] . '</span>MSRP value';
							echo '</div>';
							echo '<div class="average">';
							echo '<span class="average-value">$' . $value['StashFact']['total_paid'] . '</span>Total paid (' . $value['StashFact']['count_collectibles_paid'] . ' of ' . $value['Stash']['collectibles_user_count'] .' collectibles recorded)';
							echo '</div>';
							echo '<div class="average">';
							echo '<span class="average-value">$' . $value['StashFact']['total_sold'] . '</span>Total sold (' . $value['StashFact']['count_collectibles_sold'] . ' of ' . $value['StashFact']['count_collectibles_remove_sold'] .' removed collectibles recorded)';
							echo '</div>';
							echo '<div class="average">';
							echo '<span class="average-value">$' . $value['StashFact']['current_value'] . '</span>Collection Stash value (' . $value['StashFact']['count_collectibles_current_value'] . ' of ' . $value['Stash']['collectibles_user_count'] .' collectibles have a Collection Stash value)';
							echo '</div>';
						}
					}
					?>
					</div>
					<p class="pull-left"><a href="#" data-toggle="popover" data-placement="bottom" data-content="<h6>Total Paid</h6><p>The total paid value is an accumlation of the amount you entered in the price paid field when you added collectibles to your stash.  The number of collectibles in your stash that have a purchase amount is indicated above.</p><h6>Total Sold</h6><p>The total sold value is an accumlation of all collectibles you have removed from your stash where you indicated you sold it and added a cost.  The number of collectibles in your stash that you have sold and have a sale amount is indicated above.</p><h6>Collection Stash Value</h6><p>The Collection Stash value is an accumlation of the average price for each collectible in your Stash.  The average price is based off of completed eBay listings and user submitted sale prices.  The number of collectibles in your Stash that have an average Collection Stash price is indicated above.  You can add eBay listings to collectibles that do not have a Collection Stash average value to make this number more accurate.</p>" data-html="true" title="Why do my values look weird?" id="stash-values-weird">Why do my values look weird?</a></p>
					<p class="pull-right">
						Values update once a day.
					</p>
				</div>
			</div>
		</div>	
	</div>
	
	<div class="row spacer">
		<div class="col-md-6">
			<div class="panel panel-default user-stats">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="icon-signal"></i> Your Stats</h3>
				</div>
				<div class="panel-body">
					<div class="widget-statistics">
						<div>
							<div class="counter small">
								<span><?php
								foreach ($stashes as $key => $value) {
									if ($value['Stash']['name'] === 'Default') {
										echo $value['Stash']['collectibles_user_count'];
									}
								}
								?></span>
							</div>						
							<div class="counter-label">
								Collectibles in stash
							</div>						
						</div>
						<div>
							<div class="counter small">
								<span><?php
								foreach ($stashes as $key => $value) {
									if ($value['Stash']['name'] === 'Wishlist') {
										echo $value['Stash']['collectibles_user_count'];
									}
								}
								?></span>
							</div>						
							<div class="counter-label">
								Collectibles in Wishlist
							</div>						
						</div>
						<div>
							<div class="counter small">
								<span><?php echo $user['User']['user_upload_count']; ?></span>
							</div>						
							<div class="counter-label">
								Photos
							</div>						
						</div>					
						<div>
							<div class="counter small">
								<span><?php echo $user['User']['comment_count']; ?></span>
							</div>						
							<div class="counter-label">
								Comments
							</div>						
						</div>		
						<div>
							<div class="counter small">
								<span><?php echo $user['User']['collectible_count']; ?></span>
							</div>						
							<div class="counter-label">
								Collectibles Approved
							</div>						
						</div>	
						<div>
							<div class="counter small">
								<span><?php echo $user['User']['edit_count']; ?></span>
							</div>						
							<div class="counter-label">
								Edits Approved
							</div>						
						</div>	
	
						<div>
							<div class="counter small">
								<span><?php echo $user['User']['points']; ?></span>
							</div>						
							<div class="counter-label">
								Nuts
							</div>						
						</div>		
						
						<div>
							<div class="counter small">
								<span><?php echo $pointsMonth; ?></span>
							</div>						
							<div class="counter-label">
								Nuts earned this month
							</div>						
						</div>		
						<div>
							<div class="counter small">
								<span><?php echo $previousPointsMonth; ?></span>
							</div>						
							<div class="counter-label">
								Nuts earned last month
							</div>						
						</div>		
						<div>
							<div class="counter small">
								<span><?php echo $pointsYear; ?></span>
							</div>						
							<div class="counter-label">
								Nuts earned this year
							</div>						
						</div>			
					</div>
				</div>					
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="icon-trophy"></i> Nut Leaders <a href="/pages/nuts" class="pull-right"><i class="icon-question-sign"></i></a></h3>
				</div>
				<div class="panel-body">
					<h4>Current Month Leaders</h4>
					<ol>
						<?php
						foreach ($monthlyLeaders as $key => $value) {
							echo '<li>' . $value['User']['username'] . ' with ' . $value['UserPointFact']['points'] . ' nuts</li>';
						}
						?>
					</ol>
					<h4>Previous Month Leaders</h4>
					<ol>
						<?php
						foreach ($previousMonthlyLeaders as $key => $value) {
							echo '<li>' . $value['User']['username'] . ' with ' . $value['UserPointFact']['points'] . ' nuts</li>';
						}
						?>
					</ol>	
					<h4><?php echo date('Y')?> Leaders</h4>				
					<ol>
						<?php
						foreach ($yearlyLeaders as $key => $value) {
							echo '<li>' . $value['User']['username'] . ' with ' . $value['UserPointYearFact']['points'] . ' nuts</li>';
						}
						?>
					</ol>	
				</div>
			</div>
		</div>
	</div>
	
	<div class="row spacer">
		<div class="col-md-12">
			<div class="panel panel-default work">
				<div class="panel-heading">
					<h3 class="panel-title">What you are working on</h3>
				</div>
				
				<div class="panel-footer"></div>
			</div>
		</div>
	</div>
	
	<div class="row spacer">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Pending Collectibles</h3>
				</div>
				<div class="panel-body pending">
				
				</div>
			</div>
		</div>
	
	</div>
	
	<div class="row spacer">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Recently Approved Collectibles</h3>
				</div>
				<div class="panel-body new">
				
				</div>
			</div>
		</div>
	
	</div>
</div>

<div class="col-md-4 home">
	<div class="row spacer">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-header">
					<i class="icon-time"></i>
					<h3>Activity</h3>		
				</div>
				<div class="widget-content activities-container widget-users-activity">
					
				</div>				
			</div>
		</div>
	</div>
</div>

<script>
var userId =<?php echo $user['User']['id']; ?>;

var totalPending =<?php echo $totalPending; ?>;
var totalPendingPages = Math.ceil(totalPending / 5);
var totalNew =<?php echo $totalNew; ?>;
var totalNewPages = Math.ceil(totalPending / 5);
var totalWorks =<?php echo $totalWorks; ?>;
var totalWorkPages = Math.ceil(totalWorks / 10);
var totalActivity =<?php echo $totalActivity; ?>;
var totalActivityPages = Math.ceil(totalActivity / 10); 
</script>
<?php echo $this -> Html -> script('views/view.activity', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.paging', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.user.home', array('inline' => true)); ?>

<script>

var pending = new PaginatedPending();
pending.reset(<?php echo $pending; ?>);
var uploadDirectory =   "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>";

var newCollectibles = new PaginatedNew();
newCollectibles.reset(<?php echo $newCollectibles; ?>);

var works = new PaginatedWorkCollection();
works.reset(<?php echo $works; ?>);

var activity = new PaginatedActivityCollection();
activity.reset(<?php echo $activity; ?>);

var serverTime = '<?php echo date('Y-m-d H:i:s'); ?>';

$('#stash-values-weird').popover({trigger: 'hover'});
</script>