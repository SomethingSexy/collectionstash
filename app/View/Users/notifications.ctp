<div class="panel panel-default widget-messages">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i> Notifications</h3>
	</div>
	<div class="panel-body">

	</div>
	<div class="panel-footer">
		<ul class="_pagination"></ul>
	</div>
</div>


<script>
var totalNotifications = <?php echo $totalNotifications; ?>;
var totalNotificationPages = Math.ceil(totalNotifications / 25);
</script>
<?php echo $this -> Minify -> script('/bower_components/simplePagination/jquery.simplePagination', array('inline' => false)); ?>
<?php echo $this -> Html -> script('/bower_components/backbone.paginator/lib/backbone.paginator', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.user.home.notifications', array('inline' => true)); ?>

<script>

var notifications = new PaginatedNotifications();
notifications.reset(<?php echo json_encode($notifications); ?>, {parse: true});

var serverTime = '<?php echo date('Y-m-d H:i:s');?>';
</script>