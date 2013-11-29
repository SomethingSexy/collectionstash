
<div class="comment" style="padding: 8px 8px; background-color: #FFF; overflow: hidden;">
	<img src="<?php echo Configure::read('Settings.domain'); ?>/img/icon/avatar.png" alt="" style="border-radius: 3px; display: block; float: left; height: 30px; margin-top: 5px; width: 30px;">
	<div class="content" style="margin-left: 45px">
		<span class="commented-by" style="color: #888888; display: block; font-style: italic;"><a href="<?php echo Configure::read('Settings.domain') . '/stashs/view/' . $User['username'] ?>"><?php echo $User['username'] ?></a></span>
		<span class="comment-text"><?php echo $Comment['comment'] ?></span>
		<span class="actions" style="display: block; color: #BBBBBB; font-size: 12px;">
			<span class="pull-right" style="float: right;"><?php echo $Comment['formatted_created'] ?></span>
		</span>
	</div>
</div>