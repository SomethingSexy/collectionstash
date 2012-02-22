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
				<h2><?php echo __('Manufacturer Detail');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="manufacturer view">
			<div id="tabs">
				<ul>
					<li>
						<a href="#tabs-2"><?php echo __('Licenses');?></a>
					</li>
					<li>
						<a href="#tabs-3"><?php echo __('Collectible Types');?></a>
					</li>
				</ul>
				<div id="tabs-1">

				</div>
				<div id="tabs-2">
					<div class="licenses">
						<h3><?php echo __('Licenses');?></h3>
					
							<?php echo $this -> Html -> link(__('Add License'), array('action' => 'add_license', $manufacture['Manufacture']['id']));?>
					<div class="standard-list">
					<ul>
					<?php
						foreach ($licenses as $key => $license) {
							echo '<li>';
							echo '<span class="name">';
							echo $license['License']['name'];
							echo '</span>';
							echo '</li>';	
						}
					?>
					</ul>
				</div>		
					
					</div>
				</div>
				<div id="tabs-3">

				</div>
			</div>				
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$("#tabs").tabs();
	});
</script>