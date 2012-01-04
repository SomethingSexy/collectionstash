<div class="component-search-input">
	<?php echo $this -> Form -> create(false, array('type' => 'get', 'url' => $searchUrl));?>
		<input class="searchfield" name="q" type="text" value="">
		<input class="searchbutton" type="submit" value="Search">
	<?php echo $this -> Form -> end();?>
</div>
