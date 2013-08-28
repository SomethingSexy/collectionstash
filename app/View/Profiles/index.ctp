<?php echo $this -> Minify -> script('js/jquery.form', array('inline' => false));?>
<?php echo $this -> Html -> script('pages/page.profile', array('inline' => false));?>

<div class="widget">
	<div class="widget-header">
		<i class="icon-user"></i>
		<h3><?php echo __('Your Account'); ?></h3>
	</div>	
	<div class="widget-content">
		<div class="tabbable">
			<ul id="profile-tabs" class="nav nav-tabs">
			  <li class="active">
			    <a data-toggle="tab" href="#privacy">Privacy</a>
			  </li>
			  <li class="">
			    <a data-toggle="tab" href="#notifications">Notifications</a>
			  </li>
			</ul>			
			<br>
			<div class="tab-content">
				<div id="privacy" class="tab-pane active">
					<div class="alert alert-success hide"></div>	
					<div class="alert alert-error hide"></div>		
					<p><?php echo __('Select your privacy settings below.') ?></p>
					<form id="stash-form" method="post" class="form-horizontal">
						<fieldset>
							<div class="control-group">
								<label class="control-label" for="stash-privacy"><?php echo __('Stash Privacy') ?></label>	
								<div class="controls">
									<select id="stash-privacy" name="data[Stash][privacy]">
										<option value="0"<?php if($stashProfileSettings['privacy'] === "0"){ echo 'selected="selected"'; }?>><?php echo __('Everyone'); ?></option>
										<option value="1"<?php if($stashProfileSettings['privacy'] === "1"){ echo 'selected="selected"'; }?>><?php echo __('Registered Members'); ?></option>
										<option value="2"<?php if($stashProfileSettings['privacy'] === "2"){ echo 'selected="selected"'; }?>><?php echo __('Private'); ?></option>
									</select>									
									
								</div>
							</div>
							<div class="form-actions">
								<button class="btn btn-primary" data-loading-text="Saving..." id="stash-submit"><?php echo __('Save'); ?></button>	
							</div>
						</fieldset>
					</form>
				</div>	
			</div>
		</div>	
	</div>
</div>


<script>
	<?php echo 'var profileData = ' .  json_encode($profile['Profile']);?>
</script>
		
