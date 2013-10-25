<div class="col-md-8">
	<div class="row spacer">
		<div class="panel panel-default widget-messages">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="icon-warning-sign"></i> Notifications</h3>
			</div>
			<div class="panel-body">

			</div>
			<div class="panel-footer">
				
			</div>
		</div>
	</div>
</div>

<script>
var totalNotifications = <?php echo $totalNotifications; ?>;
var totalNotificationPages = Math.ceil(totalNotifications / 25);
</script>
<?php echo $this -> Html -> script('views/view.paging', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.user.home.notifications', array('inline' => true)); ?>

<script>

var notifications = new PaginatedNotifications();
notifications.reset(<?php echo json_encode($notifications); ?>, {parse: true});

var serverTime = '<?php echo date('Y-m-d H:i:s');?>';
</script>