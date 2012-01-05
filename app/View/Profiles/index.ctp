<?php echo $this->element('account_top'); ?>
<div class="title">
	<h2><?php echo __('Settings');?></h2>
</div>
<ul class="account page list">
	<li class="page list item" id="account-profile">
		<div class="header"><span class="page"><?php echo __('Profile');?></span><span class="action"><a class="link"><?php echo __('View');?></a></span></div>
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
					<input type="button" class="button" value="Invite" id="invite-user" /> 
				</div>
				<div class="account detail update">
					<div class="account directional">
						<p><?php echo __('To invite someone to Collection Stash, please enter their email address below.  We will send them an e-mail with details on how to register.') ?></p>
					</div>
					<form id="invite-form" method="post">
						<fieldset>
							<legend></legend>
							<ul class="form-fields">
								<li>
									<div class="label-wrapper">
										<label for="invite-email"><?php echo __('Email') ?></label>
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
		<div class="header"><span class="page"><?php echo __('Stash');?></span><span class="action"><a class="link"><?php echo __('View');?></a></span></div>
		<div class="body">
			<div class="account detail update">
				<div class='component-message'><span></span></div>				
				<div class="account directional">
					<p><?php echo __('To make your stash private, please check the box below and submit.') ?></p>
				</div>
				<form id="stash-form" method="post">
					<fieldset>
						<legend></legend>
						<ul class="form-fields">
							<li>
								<div class="label-wrapper">
									<label for="stash-privacy"><?php echo __('Private Stash?') ?></label>
								</div>
								<input id="stash-privacy" type="checkbox" value=""/>
								<input id="stash-privacy-hidden" type="hidden" value="" name="data[Stash][privacy]" value=""/>
							</li>
						</ul>
					</fieldset>
				</form>
				<input type="button" class="button" value="Submit" id="stash-submit" /> <input type="button" class="button" value="Cancel" id="stash-cancel" />
			</div>				
			
		</div>
	</li>
</ul>
<?php echo $this->element('account_bottom'); ?>





