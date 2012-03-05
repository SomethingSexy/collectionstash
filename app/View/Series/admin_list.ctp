<?php 
echo $this -> Html -> script('jquery.treeview', array('inline' => false));
echo $this -> Html -> script('cs.core.tree', array('inline' => false));
echo $this -> Html -> script('cs.series.admin', array('inline' => false));
?>

<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Categories');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="series view">
				<?php 
				echo $this -> Tree ->generate($stuff, array('id' => 'tree','element'=>'tree_series_node')); 
				
				
				
				?> 

			</div>
		</div>
	</div>
</div>
