<?php 
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('js/cs.type.admin', array('inline' => false));
?>


<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Add Collectibletype');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="series view">
				<?php 
				echo $this -> Tree ->generate($collectibletypes, array('id' => 'tree','element'=>'tree_collectibletype_node')); 
				
				
				
				?> 

			</div>
		</div>
	</div>
</div>
