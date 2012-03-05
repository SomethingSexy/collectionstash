<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Brand List');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="licenses view">
				<?php
				foreach ($licenses as $license):
				?>
				<div class="manufacture item">
					<div class="manufacture detail">
						<span class="manufacture name"><?php echo $this -> Html -> link($license['License']['name'], array('action' => 'view', $license['License']['id']));?></span>
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
</div>