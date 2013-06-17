<div class="signup-container">

	<a class="header" title="CollectionStash" href="/"> <img src="/img/icon/add_stash_link.png"> <span> Sign up for
		<br>
		<strong>Collection Stash</strong> </span> </a>
	<?php echo $this -> Form -> create('User', array('class' => 'form-horizontal', 'action' => 'register')); ?>
	<div class="fields">
		<?php echo $this -> Form -> input('username', array('div' => false, 'label' => false, 'placeholder' => 'User Name', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>
		<?php echo $this -> Form -> password('new_password', array('div' => false, 'label' => false, 'placeholder' => 'Password', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline'))));
			if ($this -> Form -> isFieldError('new_password')) {
				echo '<span class="help-inline">' . $this -> Form -> error('new_password') . '</span>';
			}
		?>

		<?php echo $this -> Form -> password('confirm_password', array('div' => false, 'label' => false, 'placeholder' => 'Confirm Password', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>

		<?php echo $this -> Form -> input('first_name', array('div' => false, 'label' => false, 'placeholder' => 'First Name', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>

		<?php echo $this -> Form -> input('last_name', array('div' => false, 'label' => false, 'placeholder' => 'Last Name', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>

		<?php echo $this -> Form -> input('email', array('div' => false, 'label' => false, 'placeholder' => 'Email', 'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')))); ?>
	</div>
	<button type="submit" class="btn btn-block">
		Submit
	</button>

	<?php echo $this -> Form -> end(); ?>
</div>

<script type="text/javascript">
	$(function() {
		var updateBoxPosition = function() {

			var margin = (($(window).height() - $('.signup-container').height()) / 2);

			margin = margin < 30 ? 30 : margin;

			$('.signup-container').css({
				'margin-top' : margin
			});
		};
		$(window).resize(updateBoxPosition);
		setTimeout(updateBoxPosition, 50);
	}); 
</script>
