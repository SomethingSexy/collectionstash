<div id="tags-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Tags') ?></h2>      
    </div>
    <div class="component-view">
		<ul class="tag-list">
			<?php 
				foreach($tags as $tag) {
					echo '<li class="tag">';
					echo '<a href="/collectibles/search/?t='.$tag['Tag']['id'].'"';
					echo '>'.$tag['Tag']['tag'].'</a>';
					echo '</li>';
				} ?>
		</ul>
    </div>    
  </div>
</div>