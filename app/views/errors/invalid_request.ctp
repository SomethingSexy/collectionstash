<div class="component" id="invalid-request-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Invalid Request');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<p><?php __('The URL or request you are trying to access is invalid.'); ?></p>
		</div>
	</div>
</div>