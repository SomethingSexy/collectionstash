<?php echo $this->Html->script('invite-account',array('inline'=>false)); ?>
<?php echo $this->element('account_top'); ?>
<div class="account title invite">
  	<h2><?php echo __('Invites'); ?></h2>
  	<?php if($allowInvites) { ?>
		<div class="title link">
			<a class="link"><?php __('Invite');?></a>
		</div>	  		
  	<?php } ?>

</div>	
<div class="account detail">
	<div class="account detail view">
		<div class="account directional">
			<p><?php __('You have '); echo $invitesLeft; __(' invites left.'); ?></p>
		</div>
		<?php
		$lastKey = 0;
		$attributeEmpty = empty($invites['Invite']);
		if($attributeEmpty){
			echo '<div class="list empty">';
			echo '<ul>';
			echo '<li>You have not invited anyone to Collection Stash.</li>';	
			echo '</ul>';
			echo '</div>';						
		} else {
			echo '<div class="list">';
			echo '<ul>';
			echo '<li class="title">';
			echo '<span class="attribute-name">'.__('Part', true).'</span>';
			echo '<span class="attribute-description">'.__('Description', true).'</span>';
			echo '</li>';
			foreach($collectibleCore['Invite'] as $key => $attribute) {
					
		
			}								
			echo '</ul>';
			echo '</div>';								
		} ?>
	</div>
	<div class="account detail update">
		<div class="account directional">
			<p><?php __('To invite someone to Collection Stash, please enter their email address below.  We will send them an e-mail with details on how to register.') ?></p>
		</div>
		<form id="invite-form">
			<fieldset>
				<legend></legend>
				<ul class="form-fields">
					<li>
						<div class="label-wrapper">
						<label for="CollectibleName"><?php __('Email') ?></label>
						</div>
						<input id="invite-email" type="text" maxlength="255" name="data[Invite][email]">
					</li>
				</ul>
			</fieldset>
			<input type="button" class="button" value="Invite" id="invite-submit" /> <input type="button" class="button" value="Cancel" id="invite-cancel" />
		</form>
	</div>
</div>	
<?php echo $this->element('account_bottom'); ?>


