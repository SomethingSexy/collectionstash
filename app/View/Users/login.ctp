<?php $this -> set("bodyClass", 'login');?>

<div class="signin-container">

	<a class="header" title="CollectionStash" href="/"> <img src="/img/icon/add_stash_link.png"> <span> Sign in to
		<br>
		<strong>Collection Stash</strong> </span> </a>

	<?php echo $this -> Form -> create('User', array('action' => 'login', 'class' => 'form-horizontal')); ?>

	<div class="fields">
		<?php echo $this -> element('flash'); ?>
		<input id="UserUsername" type="text" value="" placeholder="Username" maxlength="50" name="data[User][username]">
		<?php echo $this -> Form -> input('password', array('label' => __('Password'), 'div' => false, 'label' => false, 'placeholder' => 'Password', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>
	</div>
	<?php echo $this -> Html -> link('Forgot Password?', array('admin' => false, 'action' => 'forgotPassword', 'controller' => 'forgotten_requests'), array('class' => 'forgot-password')); ?>
	<div class="control-group  remember-me">
		<div class="controls">
			<label class="checkbox"> <?php echo $this -> Form -> input('auto_login', array('type' => 'checkbox', 'label' => false, 'div' => false)); ?>
				Remember me</label>
		</div>
	</div>
	<button type="submit" class="btn btn-primary btn-block">
		Sign in
	</button>

	<?php echo $this -> Form -> end(); ?>
</div>

<script type="text/javascript">
	$(function() {
		var updateBoxPosition = function() {

			var margin = (($(window).height() - $('.signin-container').height()) / 2);

			margin = margin < 30 ? 30 : margin;

			$('.signin-container').css({
				'margin-top' : margin
			});
		};
		$(window).resize(updateBoxPosition);
		setTimeout(updateBoxPosition, 50);
	}); 
</script>
