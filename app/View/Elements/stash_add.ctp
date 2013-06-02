<?php // this is actually going to be a dust template but I am loading it via PHP and placing it on the page for quickness ?>
<div class="well">
	<p>You are adding {name|s} to your stash.</p>
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
	{@eq key="{numbered}" value="true" type="boolean"}
		<div class="control-group {#inlineErrors.edition_size}error{/inlineErrors.edition_size}">
			<label class="control-label" for="CollectiblesUserEditionSize">Edition Number (Total: {edition_size})</label>
			<div class="controls">
				<input type="number" id="CollectiblesUserEditionSize" name="edition_size" value="{model.edition_size}">
					{#inlineErrors.edition_size}
						<span class="help-inline">{.}</span>
					{/inlineErrors.edition_size}
			</div>
		</div>		
	{/eq}
	<div class="control-group {#inlineErrors.artist_proof}error{/inlineErrors.artist_proof}">
		<label class="control-label" for="CollectiblesUserArtistProof">Artist's Proof</label>
		<div class="controls">
			<input type="checkbox" id="CollectiblesUserArtistProof" value="1" name="artist_proof" {#model.artist_proof}checked{/model.artist_proof}>
			{#inlineErrors.artist_proof}
				<span class="help-inline">{.}</span>
			{/inlineErrors.artist_proof}
		</div>
	</div>				
	<div class="control-group {#inlineErrors.cost}error{/inlineErrors.cost}">
		<label class="control-label" for="dialogCost">How much did you pay? (Retail:${msrp})</label>
		<div class="controls">
			<input type="number" maxlength="23" step="any" id="dialogCost" name="cost" value="{model.cost}">
				{#inlineErrors.cost}
					<span class="help-inline">{.}</span>
				{/inlineErrors.cost}
		</div>
	</div>					
	<div class="control-group {#inlineErrors.condition_id}error{/inlineErrors.condition_id}">
		<label class="control-label" for="CollectiblesUserConditionId">Condition</label>
		<div class="controls">
			<select id="CollectiblesUserConditionId" name="condition_id">
				<option value=""></option>
				<option value="14">Fair</option>
				<option value="11">Fine</option>
				<option value="13">Good</option>
				<option value="9">Loose</option>
				<option value="5">Mint and Complete</option>
				<option value="2">Mint in Box</option>
				<option value="3">Mint in package</option>
				<option value="4">Mint on card</option>
				<option value="6">Mint, no box</option>
				<option value="7">Near Mint</option>
				<option value="8">Never removed from box</option>
				<option value="1">New</option>
				<option value="15">Poor</option>
				<option value="10">Very Fine</option>
				<option value="12">Very Good</option>
			</select>
			{#inlineErrors.condition_id}
				<span class="help-inline">{.}</span>
			{/inlineErrors.condition_id}
		</div>
	</div>	
	<div class="control-group {#inlineErrors.merchant}error{/inlineErrors.merchant}">
		<label class="control-label" for="CollectiblesUserMerchantId">Where did you purchase the collectible?</label>
		<div class="controls">
			<input type="text" id="CollectiblesUserMerchantId" maxlength="150" name="merchant" autocomplete="off" value="{model.merchant}">
			{#inlineErrors.merchant}
				<span class="help-inline">{.}</span>
			{/inlineErrors.merchant}
		</div>
	</div>			
	<div class="control-group {#inlineErrors.purchase_date}error{/inlineErrors.purchase_date}">
		<label class="control-label" for="CollectiblesUserPurchaseDate">When did you purchase this collectible?</label>
		<div class="controls">
			<input type="text" id="CollectiblesUserPurchaseDate" maxlength="8" name="purchase_date" value="{model.purchase_date}">
			{#inlineErrors.purchase_date}
				<span class="help-inline">{.}</span>
			{/inlineErrors.purchase_date}
		</div>
	</div>			
</form>
</div>

