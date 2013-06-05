<?php // this is actually going to be a dust template but I am loading it via PHP and placing it on the page for quickness ?>
<div class="well">
	<p>You are removing {name|s} from your stash.</p>
</div>	
{@if cond=" ('{errors}'.length === 0) "}

{:else}
	<div class="alert {@eq key="{hasErrors}" value="true" type="boolean"}alert-error{/eq}">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Oops! Something went wrong.</h4>
		<ul>
		{#errors}
		{@eq key="inline" value="false" type="boolean" }
			{#message}
			<li>{.}</li>
			{/message}
		{/eq}
		{/errors}
		</ul>
	</div>
{/if}	

<form class="form-horizontal">
	<div class="control-group {#inlineErrors.collectible_user_remove_reason_id}error{/inlineErrors.collectible_user_remove_reason_id}">
		<label class="control-label" for="CollectiblesUserRemoveReason">Reason</label>
		<div class="controls">
			<select required id="CollectiblesUserRemoveReason" name="collectible_user_remove_reason_id">
				<option value=""></option>
			{#reasons collectible_user_remove_reason_id=model.collectible_user_remove_reason_id}
				<option {@eq key="{collectible_user_remove_reason_id}" value="{CollectibleUserRemoveReason.id}"}selected{/eq} value="{CollectibleUserRemoveReason.id}">{CollectibleUserRemoveReason.reason}</option>
			{/reasons}
			</select>
			<span class="help-inline">This is the reason why you are removing this collectible.</span>
			{#inlineErrors.collectible_user_remove_reason_id}
				<span class="help-inline">{.}</span>
			{/inlineErrors.collectible_user_remove_reason_id}
		</div>
	</div>	
	{@eq key="{model.collectible_user_remove_reason_id}" value="1"}
		<p>Removing this collectible by indicating you sold it will keep the collectible in your history.  It will also add a listing and transaction to the collectible.</p>
		<div class="control-group {#inlineErrors.sold_cost}error{/inlineErrors.sold_cost}">
			<label class="control-label" for="CollectiblesUserRemoveCost">How much did you sell it for?</label>
			<div class="controls">
				<input required type="number" maxlength="23" step="any" id="CollectiblesUserRemoveCost" name="cost" value="{model.sold_cost}">
					{#inlineErrors.sold_cost}
						<span class="help-inline">{.}</span>
					{/inlineErrors.sold_cost}
			</div>
		</div>					
	
			
		<div class="control-group {#inlineErrors.remove_date}error{/inlineErrors.remove_date}">
			<label class="control-label" for="CollectiblesUserRemoveDate">When did you sell this collectible?</label>
			<div class="controls">
				<input required type="text" id="CollectiblesUserRemoveDate" maxlength="8" name="remove_date" value="{model.remove_date}">
				{#inlineErrors.remove_date}
					<span class="help-inline">{.}</span>
				{/inlineErrors.remove_date}
			</div>
		</div>			
	{/eq}
			
	
</form>
</div>

