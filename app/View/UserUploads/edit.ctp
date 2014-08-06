<?php
echo $this -> Html -> script('/bower_components/backbone.bootstrap-modal/src/backbone.bootstrap-modal', array('inline' => false));
echo $this -> Minify -> script('models/model.userupload', array('inline' => false));
echo $this -> Minify -> script('collections/collection.useruploads', array('inline' => false));
echo $this -> Minify -> script('pages/page.useruploads.edit', array('inline' => false));
?>
<div id="user-uploads-component" class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-camera"></i> Photos</h3>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="panel-body">
			<div class="btn-group actions">
				<?php
				if (Configure::read('Settings.User.uploads.allowed')) {
					echo '<a title="Upload Photos" class="btn" href="/user_uploads/uploads"><i class="fa fa-camera"></i> Upload and Delete</a>';
				}
				?>
			</div>
			<div class="stats pull-right">
				<span class="count"><?php echo __('Images') . ': <span id="user-count">' . count($userUploads) . '</span>/' . Configure::read('Settings.User.uploads.total-allowed'); ?></span>
			</div>
			<div id="uploads-container">
				
			</div>
		</div>
</div>
<script>
var userUploads = new UserUploadsCollection();
userUploads.reset(<?php echo json_encode($userUploads); ?>);</script>