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
				<h2><?php echo __('Add License to Manufacturer');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="licenses view">
				<?php echo $this -> Form -> create('LicensesManufacture', array('url' => '/admin/manufactures/add_license/' . $manufacture_id, 'id' => 'add-form')); ?>
				<div class="standard-list">
					<ul>
					<?php
						foreach ($licenses as $key => $license) {
							echo '<li>';
							echo '<span class="name">';
							echo $license['License']['name'];
							echo '</span>';
							echo '<span class="action">';
							echo '<input type="checkbox" name="data[LicensesManufacture]['.$key.'][license_id]" value="'.$license['License']['id'] .'"/>';
							echo '</span>';							
							echo '</li>';	
						}
					?>
					</ul>
				</div>	

				<?php echo $this -> Form -> end(__('Add', true));?>
				
			</div>
		</div>
	</div>
</div>