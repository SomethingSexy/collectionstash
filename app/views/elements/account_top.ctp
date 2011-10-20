<?php echo $this -> Html -> script('jquery.form', array('inline' => false));?>
<?php echo $this -> Html -> script('profiles', array('inline' => false));?>
<div id="account" class="two-column-page">
	<div class="inside">
		<div class="actions">
			<ul>
				<li>
					<?php echo $this -> Html -> link('Account Settings', '/profiles/index', array('class' => 'button'));?>
				</li>
				<li>
					<?php echo $this -> Html ->link('History', '/collectibles/userHistory', array('class' => 'button'));?>
				</li>
				<li>
					<?php //echo $html->link('Stats', array('controller' => 'stashs','action'=>'stats'));?>
				</li>
			</ul>
		</div>
		<div class="account page">
