<div class="span8">
	<div class="row-fluid spacer">
		<div class="widget widget-messages">
			<div class="widget-header">
				<i class="icon-warning-sign"></i><h3>Notifications</h3>
			</div>
			<div class="widget-content">

			</div>
		</div>
	</div>
</div>

<script>
var totalNotifications = <?php echo $totalNotifications; ?>;
var totalNotificationPages = Math.ceil(totalNotifications / 25);
</script>
<?php echo $this -> Html -> script('pages/page.user.home.notifications', array('inline' => true)); ?>

<script>

var notifications = new PaginatedNotifications();
notifications.reset(<?php echo json_encode($notifications); ?>, {parse: true});

var serverTime = '<?php echo date('Y-m-d H:i:s');?>';
</script>