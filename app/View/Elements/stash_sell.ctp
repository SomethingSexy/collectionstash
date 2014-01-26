<?php // this is actually going to be a dust template but I am loading it via PHP and placing it on the page for quickness ?>
<div class="well">
	<p>
		You are selling/trading {name|s} from your stash.
	</p>
</div>
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
	<div class="radio">
		<label>
			<input type="radio" name="listing_type_id" value="2" {@eq key="{model.listing_type_id}" value="2"}checked{/eq}>
			Sell this collectible.</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="listing_type_id" value="3" {@eq key="{model.listing_type_id}" value="3"}checked{/eq}>
			Trade this collectible. </label>
	</div>
	{#inlineErrors.listing_type_id}
	<span class="help-inline">{.}</span>
	{/inlineErrors.listing_type_id}	
	{@eq key="{model.listing_type_id}" value="2"}
	<div class="form-group {#inlineErrors.sold_cost}has-error{/inlineErrors.sold_cost}">
		<label class="control-label" for="CollectiblesUserRemoveCost">How much do you want to sell it for?</label>
		<input required type="number" maxlength="23" step="any" id="CollectiblesUserRemoveCost" class="form-control" name="sold_cost" value="{model.sold_cost}" >
		{#inlineErrors.sold_cost}
		<span class="help-inline">{.}</span>
		{/inlineErrors.sold_cost}			
	</div>
	{/eq}

	{@eq key="{model.listing_type_id}" value="3"}
	<div class="form-group {#inlineErrors.traded_for}has-error{/inlineErrors.traded_for}">
		<label class="control-label" for="CollectiblesUserTradedFor">What did you want to trade for this collectible for?</label>
		<textarea id="CollectiblesUserTradedFor" class="form-control" name="traded_for">{model.traded_for|s}</textarea>
		{#inlineErrors.traded_for}
		<span class="help-inline">{.}</span>
		{/inlineErrors.traded_for}			
	</div>
	{/eq}

</form>
</div>

