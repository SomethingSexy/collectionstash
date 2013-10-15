<div class="col-md-12 home">
	<div class="row spacer">
		<div class="col-md-6">
			<div class="widget widget-table">
				<div class="widget-header">
					<h3>Your Submissions</h3>
				</div>
				<div class="widget-content submissions">
				
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="widget widget-table">
				<div class="widget-header">
					<h3>Your Edits</h3>
				</div>
				<div class="widget-content edits">
				
				</div>
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

</script>
<?php echo $this -> Html -> script('pages/page.user.home.activity', array('inline' => true)); ?>
<script>

var submissions = new PaginatedCollection();
submissions.reset(<?php echo $collectibles; ?>);

var edits = new PaginatedEdits();
edits.reset(<?php echo $edits; ?>);


var serverTime = '<?php echo date('Y-m-d H:i:s');?>';
</script>