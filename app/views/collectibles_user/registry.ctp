<div class="component">
	<div class="inside">
		<div class="component-title">
			<h2><h2><?php  __('Registry');?></h2></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<?php if(isset($registry) && !empty($registry)){
				
			} else {
				echo '<div class="standard-list empty">';
				echo '<ul>';
				echo '<li>No one owns this collectible.</li>';
				echo '</ul>';
				echo '</div>';				
			}?>
		</div>
	</div>
</div>
