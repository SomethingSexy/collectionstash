<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Add Brand to Manufacturer');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="licenses view">
				<?php echo $this -> Form -> create('LicensesManufacture', array('url' => '/admin/manufactures/add_license/' . $manufacturer_id, 'id' => 'add-form')); ?>
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