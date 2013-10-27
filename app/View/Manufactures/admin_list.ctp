<?php echo $this -> element('admin_actions');?>
<div class="col-md-8">
	<div class="page">
		<div class="title">
			<h2><?php echo __('Manufacturer List');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="manufactures view">
			<?php
			foreach ($manufacturers as $manufacture):
			?>
			<div class="manufacture item">
				<div class="manufacture detail">
					<span class="manufacture name"><?php echo $this -> Html -> link($manufacture['Manufacture']['title'], array('action' => 'view', $manufacture['Manufacture']['id']));?></span>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
</div>
