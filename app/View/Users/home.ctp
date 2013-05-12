
<div class="span3">
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="well activities">
				<h3>Activity</h3>	
				
			</div>
			
		</div>
	</div>
</div>
<div class="span9">
	<div class="row-fluid spacer">
		<div class="span6">
			<div class="well user-stats">
				<h3>My Stats</h3>
				<dl class="dl-horizontal">
					<dt>
						Stash Count:
					</dt>
					<dd>
						<?php
						foreach ($stashes as $key => $value) {
							if ($value['Stash']['name'] === 'Default') {
								echo $value['Stash']['collectibles_user_count'];
							}
						}
						?>
					</dd>
					<dt>
						Wishlist Count:
					</dt>
					<dd>
						<?php
						foreach ($stashes as $key => $value) {
							if ($value['Stash']['name'] === 'Wishlist') {
								echo $value['Stash']['collectibles_user_count'];
							}
						}
						?>
					</dd>
					<dt>
						Photo Count:
					</dt>
					<dd>
						<?php echo $user['User']['user_upload_count']; ?>
					</dd>
					<dt>
						Comment Count:
					</dt>
					<dd>
						<?php echo $user['User']['comment_count']; ?>
					</dd>
					<dt>
						Collectibles Approved:
					</dt>
					<dd>
						<?php echo $user['User']['collectible_count']; ?>
					</dd>
					<dt>
						Edits Approved:
					</dt>
					<dd>
						<?php echo $user['User']['edit_count']; ?>
					</dd>
					<dt>
						Total Invites:
					</dt>
					<dd>
						<?php echo $user['User']['invite_count']; ?>
					</dd>
				</dl>
				<h4>Points</h4>
				<dl class="dl-horizontal">
					<dt>
						Total Nuts:
					</dt>
					<dd>
						<?php echo $user['User']['points']; ?>
					</dd>
					<dt>
						Nuts earned this month:
					</dt>
					<dd>
						<?php echo $pointsMonth; ?>
					</dd>
					<dt>
						Nuts earned last month:
					</dt>
					<dd>
						<?php echo $previousPointsMonth; ?>
					</dd>
					<dt>
						Nuts earned this year:
					</dt>
					<dd>
						<?php echo $pointsYear; ?>
					</dd>
	
				</dl>
			</div>
	
		</div>
		<div class="span6">
			<div class="well">
				<h3>Nut Stats <a href="/pages/nuts"><i class="icon-question-sign"></i></a></h3>
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
	
			</div>
		</div>
	</div>
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="well work">
				<h3>What I am working on</h3>
	
			</div>
		</div>
	</div>
	
	<div class="row-fluid spacer">
		<div class="span6">
			<div class="well submissions">
				<h3>My Submissions</h3>
	
			</div>
		</div>
		<div class="span6">
			<div class="well edits">
				<h3>My Edits</h3>
			</div>
		</div>
	</div>
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="well pending">
				<h3>Pending Collectibles</h3>
			</div>
		</div>
	
	</div>
	
	<div class="row-fluid spacer">
		<div class="span12">
			<div class="well new">
				<h3>Recently Added Collectibles</h3>
			</div>
		</div>
	
	</div>
</div>

<script>
var userId =<?php echo $user['User']['id']; ?>;
var totalSubmission = <?php echo $total; ?>;
var totalSubmissionPages = Math.ceil(totalSubmission / 10);
var totalEdit = <?php echo $totalEdits; ?>;
var totalEditPages = Math.ceil(totalEdit / 10);
var totalPending = <?php echo $totalPending; ?>;
var totalPendingPages = Math.ceil(totalPending / 10); 

var totalWorks = <?php echo $totalWorks; ?>;
var totalWorkPages = Math.ceil(totalWorks / 10); 

var totalActivity = <?php echo $totalActivity; ?>;
var totalActivityPages = Math.ceil(totalActivity / 10); 
</script>
<?php echo $this -> Html -> script('views/view.activity', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.user.home', array('inline' => true)); ?>
<script>

var submissions = new PaginatedCollection();
submissions.reset(<?php echo $collectibles; ?>);

var edits = new PaginatedEdits();
edits.reset(<?php echo $edits; ?>);

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