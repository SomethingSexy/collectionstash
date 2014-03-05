<?php echo $this -> element('admin_actions'); ?>
<div class="col-md-10">
	<div class="title">
		<h2><?php echo __('Collectibles Cache'); ?></h2>
	</div>
	<?php echo $this -> element('flash'); ?>
	<div class="row">
		<div id="message-container" class="col-md-4">
			
		</div>
	</div>
	<div id="admin-container" class="row">

	</div>
</div>

<?php echo $this -> Html -> script('views/view.alert', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.admin.collectible.cache', array('inline' => false)); ?>

