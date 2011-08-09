<?php echo $this->element('account_top'); ?>
<ul class="account page list">
	<li class="page list item" id="account-profile">
		<div class="header"><span class="page"><?php __('Profile');?></span><span class="action"><a class="link"><?php __('View');?></a></span></div>
		<div class="body">
			
			
		</div>
	</li>
	<li class="page list item" id="account-invites">
		<div class="header"><span class="page"><?php __('Invites');?></span><span class="action"><?php if($allowInvites) { echo '<a class="link">';echo __('View'); echo'</a>'; } else { echo __('Not Available');}?></span></div>
		<div class="body">
				<div class="account detail view">
					<div class="account directional">
						<p id="account-invites-left"><?php //__('You have '); echo $invitesLeft; __(' invites left.'); ?></p>
					</div>
					<div class="standard-list">
						<ul></ul>
					</div>
					<input type="button" class="button" value="Invite" id="invite-user" /> 
					<?php
					// $lastKey = 0;
					// $attributeEmpty = empty($invites['Invite']);
					// if($attributeEmpty){
						// echo '<div class="list empty">';
						// echo '<ul>';
						// echo '<li>You have not invited anyone to Collection Stash.</li>';	
						// echo '</ul>';
						// echo '</div>';						
					// } else {
						// echo '<div class="list">';
						// echo '<ul>';
						// echo '<li class="title">';
						// echo '<span class="attribute-name">'.__('Part', true).'</span>';
						// echo '<span class="attribute-description">'.__('Description', true).'</span>';
						// echo '</li>';
						// foreach($collectibleCore['Invite'] as $key => $attribute) {
// 								
// 					
						// }								
						// echo '</ul>';
						// echo '</div>';								
					// } 
					?>
				</div>
				<div class="account detail update">
					<div class="account directional">
						<p><?php __('To invite someone to Collection Stash, please enter their email address below.  We will send them an e-mail with details on how to register.') ?></p>
					</div>
					<form id="invite-form" method="post">
						<fieldset>
							<legend></legend>
							<ul class="form-fields">
								<li>
									<div class="label-wrapper">
										<label for="CollectibleName"><?php __('Email') ?></label>
									</div>
									<input id="invite-email" type="text" maxlength="255" name="data[Invite][email]" value=""/>
								</li>
							</ul>
						</fieldset>
					</form>
					<input type="button" class="button" value="Invite" id="invite-submit" /> <input type="button" class="button" value="Cancel" id="invite-cancel" />
				</div>			
		</div>
	</li>
	<li class="page list item" id="account-stash">
		<div class="header"><span class="page"><?php __('Stash');?></span><span class="action"><a class="link"><?php __('View');?></a></span></div>
		<div class="body">
			
			
		</div>
	</li>
</ul>
<?php echo $this->element('account_bottom'); ?>





