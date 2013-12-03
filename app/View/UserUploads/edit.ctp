<?php
echo $this -> Minify -> script('js/thirdparty/backbone.bootstrap-modal', array('inline' => false));
echo $this -> Minify -> script('js/models/model.userupload', array('inline' => false));
echo $this -> Minify -> script('js/collections/collection.useruploads', array('inline' => false));
echo $this -> Minify -> script('js/thirdparty/backbone.bootstrap-modal', array('inline' => false));
echo $this -> Html -> script('pages/page.useruploads.edit', array('inline' => false));
?>
<div id="user-uploads-component" class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="icon-camera"></i> Photos</h3>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="panel-body">
			<div class="btn-group actions">
				<?php
				if (Configure::read('Settings.User.uploads.allowed')) {
					echo '<a title="Upload Photos" class="btn" href="/user_uploads/uploads"><i class="icon-camera"></i> Upload</a>';
				}
				?>
			</div>
			<div class="stats pull-right">
				<span class="count"><?php echo __('Images') . ': <span id="user-count">' . count($userUploads) . '</span>/' . Configure::read('Settings.User.uploads.total-allowed'); ?></span>
			</div>
			
			<div class="row spacer user-uploads">

			</div>
		</div>
</div>
<script>
var userUploads = new UserUploadsCollection();
userUploads.reset(<?php echo json_encode($userUploads); ?>);</script>