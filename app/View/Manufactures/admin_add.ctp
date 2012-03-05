<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Add Manufacturer');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="licenses view">
				<?php echo $this -> Form -> create('Manufacture');?>
				<fieldset>
					<ul class="form-fields">
						<li>
							<?php echo $this -> Form -> input('title', array('label'=>__('Name'),'before' => '<div class="label-wrapper">','between'=>'</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('url', array('label'=>__('URL'),'before' => '<div class="label-wrapper">','between'=>'</div>'));?>
						</li>
						<li>
							<?php echo $this -> Form -> input('series_id', array('type' => 'text', 'label'=>__('Series Id'),'before' => '<div class="label-wrapper">','between'=>'</div>'));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>