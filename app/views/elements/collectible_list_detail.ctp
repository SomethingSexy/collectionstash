<div class="collectible detail">
	<dl>
		<dt>
			Name:
		</dt>
		<dd>
			<?php echo $collectible['name'];?><?php
			if($collectible['exclusive']) { __(' - Exclusive');
			}
			?>
		</dd>
		<?php
		if($collectible['variant']) {
			echo '<dt>';
			__('Variant:');
			echo '</dt><dd>';
			__('Yes');
			echo '</dd>';
	
		}
		?>
		<dt>
			Manufacture:
		</dt>
		<dd>
			<a target="_blank" href="<?php echo $manufacture['url'];?>">
			<?php echo $manufacture['title'];?>
			</a>
		</dd>
		<dt>
			License:
		</dt>
		<dd>
			<?php echo $license['name'];?>
		</dd>
		<dt>
			Type:
		</dt>
		<dd>
			<?php echo $collectibletype['name'];?>
		</dd>
	</dl>
</div>