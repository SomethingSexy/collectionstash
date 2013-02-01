<?php echo $this -> Minify -> script('js/jquery.form', array('inline' => false));?>
<?php echo $this -> Minify -> script('js/profiles', array('inline' => false));?>
<div id="account" class="two-column-page">
	<div class="inside">
		<div class="page actions">
			<ul class="unstyled">
				<li>
					<h3>Account</h3>		
					<ul class="unstyled">
						<li><?php echo $this -> Html -> link('Account Settings', '/profiles/index', array('class' => 'link'));?></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="account page">
