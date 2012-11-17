<?php echo $this->element('account_top'); ?>
<div class="title">
	<h2><?php echo __('Settings');?></h2>
</div>
<ul class="account page list">
	<li class="page list item" id="account-profile">
		<div class="header"><span class="page"><?php echo __('Profile');?></span><span class="action"><?php echo __('Not Available');?></span></div>
		<div class="body">
			
			
		</div>
	</li>
	<li class="page list item" id="account-invites">
		<div class="header"><span class="page"><?php echo __('Invites');?></span><span class="action"><?php if($allowInvites) { echo '<a class="link">';echo __('View'); echo'</a>'; } else { echo __('Not Available');}?></span></div>
		<div class="body">
				<div class="account detail view">
					<div class="account directional">
						<p id="account-invites-left"><?php //__('You have '); echo $invitesLeft; __(' invites left.'); ?></p>
					</div>
					<div class="standard-list">
						<ul></ul>
					</div>
					<button class="btn btn-primary" id="invite-user"><?php echo __('Invite');?></button> 
				</div>
				<div class="account detail update">
					<div class="account directional">
						<p><?php echo __('To invite someone to Collection Stash, please enter their email address below.  We will send them an e-mail with details on how to register.') ?></p>
					</div>
					<form id="invite-form" method="post">
						<fieldset>
							<legend></legend>
							<ul class="form-fields unstyled">
								<li>
									<div class="label-wrapper">
										<label for="invite-email"><?php echo __('Email') ?></label>
									</div>
									<input id="invite-email" type="text" maxlength="255" name="data[Invite][email]" value=""/>
								</li>
							</ul>
						</fieldset>
					</form>
					<button class="btn btn-primary" id="invite-submit"><?php echo __('Invite');?></button> <button class="btn" id="invite-cancel"><?php echo __('Cancel');?></button>
				</div>			
		</div>
	</li>
	<li class="page list item" id="account-stash">
		<div class="header"><span class="page"><?php echo __('Stash');?></span><span class="action"><a class="link"><?php echo __('View');?></a></span></div>
		<div class="body">
			<div class="account detail update">
				<div class='component-message'><span></span></div>				
				<div class="account directional">
					<p><?php echo __('Select your privacy settings below.') ?></p>
				</div>
				<form id="stash-form" method="post">
					<fieldset>
						<legend></legend>
						<ul class="form-fields unstyled">
							<li>
								<div class="label-wrapper">
									<label for="stash-privacy"><?php echo __('Stash Privacy') ?></label>
								</div>
								<select id="stash-privacy" name="data[Stash][privacy]">
									<option value="0"><?php echo __('Everyone');?></option>
									<option value="1"><?php echo __('Registered Members');?></option>
									<option value="2"><?php echo __('Private');?></option>
								</select>
							</li>
						</ul>
					</fieldset>
				</form>
				<button class="btn btn-primary" id="stash-submit"><?php echo __('Submit');?></button> <button class="btn" id="stash-cancel"><?php echo __('Cancel');?></button>
			</div>				
			
		</div>
	</li>
</ul>
<?php echo $this->element('account_bottom'); ?>





