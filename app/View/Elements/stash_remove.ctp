<?php // this is actually going to be a dust template but I am loading it via PHP and placing it on the page for quickness ?>	
{@ne key="{model.active}" value="true" type="boolean"}
	<div class="alert alert-danger"><p>{name|s} is already inactive.  Removing it will permanently remove it from your history.</p></div>
{:else}
	<div class="alert alert-info"><p>You are removing {name|s} from your stash.</p></div>
{/ne}
{@if cond=" ('{errors}'.length === 0) "}

{:else}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">
		&times;
	</button>
	<h4>Oops! Something went wrong.</h4>
	<ul>
		{#errors}
		{@eq key="inline" value="false" type="boolean" }
		{#message}
		<li>
			{.}
		</li>
		{/message}
		{/eq}
		{/errors}
	</ul>
</div>
{/if}

<form>
	<div class="form-group {#inlineErrors.collectible_user_remove_reason_id}has-error{/inlineErrors.collectible_user_remove_reason_id}">
		<label class="control-label" for="CollectiblesUserRemoveReason">Reason</label>
		{@if cond=" ( '{model.active}' != true ||  '{model.changeReason}' == false )" }
			{#reasons collectible_user_remove_reason_id=model.collectible_user_remove_reason_id}
				 {@eq key="{collectible_user_remove_reason_id}" value="{CollectibleUserRemoveReason.id}"}<span class="input-xlarge uneditable-input"> {CollectibleUserRemoveReason.reason}</span>{/eq}
			{/reasons}
		{:else}
			<select required id="CollectiblesUserRemoveReason" class="form-control" name="collectible_user_remove_reason_id" >
				<option value=""></option>
				{#reasons collectible_user_remove_reason_id=model.collectible_user_remove_reason_id}
				<option {@eq key="{collectible_user_remove_reason_id}" value="{CollectibleUserRemoveReason.id}"}selected{/eq} value="{CollectibleUserRemoveReason.id}">{CollectibleUserRemoveReason.reason}</option>
				{/reasons}
			</select>
			<span class="help-inline">This is the reason why you are removing this collectible.</span>
			{#inlineErrors.collectible_user_remove_reason_id}
			<span class="help-inline">{.}</span>
			{/inlineErrors.collectible_user_remove_reason_id}			
		{/if}
	</div>
	{@if cond=" ( '{model.collectible_user_remove_reason_id}' == 1 ||  '{model.collectible_user_remove_reason_id}' == 2 )" }
		{@eq key="{model.collectible_user_remove_reason_id}" value="1"}
		<p>
			Removing this collectible by indicating you sold, it will keep the collectible in your history.  It will also add a listing and transaction to the collectible.
		</p>
		{:else}
		<p>
			Removing this collectible by indicating you traded it, will keep the collectible in your history.
		</p>
		{/eq}

	{@eq key="{model.collectible_user_remove_reason_id}" value="1"}
	<div class="form-group {#inlineErrors.sold_cost}has-error{/inlineErrors.sold_cost}">
		<label class="control-label" for="CollectiblesUserRemoveCost">How much did you sell it for?</label>
		{@ne key="{model.active}" value="true" type="boolean"}
			<span class="input-xlarge uneditable-input"> {model.sold_cost}</span>			
		{:else}
			<input required type="number" maxlength="23" step="any" id="CollectiblesUserRemoveCost" class="form-control" name="sold_cost" value="{model.sold_cost}" >
			{#inlineErrors.sold_cost}
			<span class="help-inline">{.}</span>
			{/inlineErrors.sold_cost}			
		{/ne}
	</div>
	{/eq}

	{@eq key="{model.collectible_user_remove_reason_id}" value="2"}
	<div class="form-group {#inlineErrors.traded_for}has-error{/inlineErrors.traded_for}">
		<label class="control-label" for="CollectiblesUserTradedFor">What did you trade this collectible for?</label>
		{@ne key="{model.active}" value="true" type="boolean"}
			<span class="input-xlarge uneditable-input"> {model.traded_for}</span>			
		{:else}
			<textarea id="CollectiblesUserTradedFor" class="form-control" name="traded_for">{model.traded_for|s}</textarea>
			{#inlineErrors.traded_for}
			<span class="help-inline">{.}</span>
			{/inlineErrors.traded_for}			
		{/ne}
	</div>
	{/eq}

	<div class="form-group {#inlineErrors.remove_date}has-error{/inlineErrors.remove_date}">
		<label class="control-label" for="CollectiblesUserRemoveDate"> {@eq key="{model.collectible_user_remove_reason_id}" value="1"}
		When did you sell this collectible?
		{:else}
		When did you trade this collectible?
		{/eq} </label>
		{@ne key="{model.active}" value="true" type="boolean"}
			<span class="input-xlarge uneditable-input"> {model.remove_date}</span>	
		{:else}
			<input required type="text" id="CollectiblesUserRemoveDate" class="form-control" maxlength="10" name="remove_date" value="{model.remove_date}" >
			{#inlineErrors.remove_date}
			<span class="help-inline">{.}</span>
			{/inlineErrors.remove_date}				
		{/ne}
		</div>
	</div>
	{/if}

</form>
</div>

