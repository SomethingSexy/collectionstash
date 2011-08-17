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
					echo '<a href="/collectibles/search?t='.$tag['Tag']['id'].'"';
					echo '>'.$tag['Tag']['tag'].'</a>';
					echo '</li>';
				} ?>
		</ul>
    </div>    
  </div>
</div>
<div id="manufactures-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Manufactures') ?></h2>      
    </div>
    <div class="component-view">
		<ul class="tag-list">
			<?php 
				foreach($manufactures as $manufacturer) {
					echo '<li class="tag">';
					echo '<a href="/collectibles/search?m='.$manufacturer['Manufacture']['id'].'"';
					echo '>'.$manufacturer['Manufacture']['title'].'</a>';
					echo '</li>';
				} ?>
		</ul>
    </div>    
  </div>
</div>
<div id="types-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Collectible Types') ?></h2>      
    </div>
    <div class="component-view">
		<ul class="tag-list">
			<?php 
				foreach($collectibletypes as $collectibletype) {
					echo '<li class="tag">';
					echo '<a href="/collectibles/search?ct='.$collectibletype['Collectibletype']['id'].'"';
					echo '>'.$collectibletype['Collectibletype']['name'].'</a>';
					echo '</li>';
				} ?>
		</ul>
    </div>    
  </div>
</div>