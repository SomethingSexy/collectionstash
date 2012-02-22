<div class="two-column-page">
	<div class="inside">
		<div class="actions">
			<ul>
				<li>
					<h3><?php echo __('Admin');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('New Collectibles', '/admin/collectibles/index', array('class' => 'link'));?>
						</li>
						<li>
							<?php echo $this -> Html -> link('Edits', '/admin/edits/index', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
				<li>
					<h3><?php echo __('Manufacturers');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('Detail', '/admin/manufactures/list', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
				<li>
					<h3><?php echo __('Series');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('View', '/admin/series/view', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
			</ul>
		</div>
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
</div>