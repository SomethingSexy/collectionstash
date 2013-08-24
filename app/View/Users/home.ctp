<div class="span8 home">
		<?php
	if ($user['User']['collectibles_user_count'] == 0) {
	?>
	<div class="row-fluid spacer">
		<div class="span12">
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

	
	<div class="row-fluid spacer">
		<div class="span6">
			<div class="widget user-stats">
				<div class="widget-header">
					<i class="icon-signal"></i><h3>Your Stats</h3>
				</div>
				<div class="widget-content">
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
		<div class="span6">
			<div class="widget">
				<div class="widget-header">
					<i class="icon-trophy"></i><h3>Nut Leaders</h3>
					<a href="/pages/nuts" class="pull-right"><i class="icon-question-sign"></i></a>
				</div>
				<div class="widget-content">
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
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="widget widget-table">
				<div class="widget-header">
					<h3>What you are working on</h3>
				</div>
				<div class="widget-content work">
					
				</div>
			</div>
		</div>
	</div>
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="widget">
				<div class="widget-header">
					<h3>Pending Collectibles</h3>
				</div>
				<div class="widget-content pending">
				
				</div>
			</div>
		</div>
	
	</div>
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="widget">
				<div class="widget-header">
					<h3>Recently Approved Collectibles</h3>
				</div>
				<div class="widget-content new">
				
				</div>
			</div>
		</div>
	
	</div>
</div>

<div class="span4 home">
	<div class="row-fluid spacer">
		<div class="span12">
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


var totalPending = <?php echo $totalPending; ?>;
var totalPendingPages = Math.ceil(totalPending / 5); 

var totalNew = <?php echo $totalNew; ?>;
var totalNewPages = Math.ceil(totalPending / 5); 

var totalWorks = <?php echo $totalWorks; ?>;
var totalWorkPages = Math.ceil(totalWorks / 10); 

var totalActivity = <?php echo $totalActivity; ?>;
var totalActivityPages = Math.ceil(totalActivity / 10); 
</script>
<?php echo $this -> Html -> script('views/view.activity', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.user.home', array('inline' => true)); ?>
<script>

var pending = new PaginatedPending();
pending.reset(<?php echo $pending; ?>);
var uploadDirectory = "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>";

var newCollectibles = new PaginatedNew();
newCollectibles.reset(<?php echo $newCollectibles; ?>);

var works = new PaginatedWorkCollection();
works.reset(<?php echo $works; ?>);

var activity = new PaginatedActivityCollection();
activity.reset(<?php echo $activity;?>);

var serverTime = '<?php echo date('Y-m-d H:i:s');?>';
</script>