<?php
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('js/cs.series.admin', array('inline' => false));
?>

<?php echo $this -> element('admin_actions'); ?>
<div class="col-md-8">
	<div class="title">
		<h2><?php echo __('Categories'); ?></h2>
	</div>
	<?php echo $this -> element('flash'); ?>
	<div class="series view">
		<?php
		echo $this -> Tree -> generate($stuff, array('id' => 'tree', 'element' => 'tree_series_node'));
		?> 
	
	</div>
</div>
