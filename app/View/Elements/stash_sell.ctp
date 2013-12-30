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
			<input type="radio" name="listing_type_id" value="3" checked>
			Sell this collectible.</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="listing_type_id" value="4" checked>
			Trade this collectible. </label>
	</div>
</form>
</div>

