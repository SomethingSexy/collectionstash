<?php // this is actually going to be a dust template but I am loading it via PHP and placing it on the page for quickness ?>
<div id="stash-add-dialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
			×
		</button>
		<h3 id="myModalLabel">Add to Stash</h3>
	</div>
	<div class="modal-body">
	<form class="form-horizontal">
		{@eq key="{numbered}" value="true" type="boolean"}
			<div class="control-group">
				<label class="control-label" for="CollectiblesUserEditionSize">Edition Number (Total: {edition_size})</label>
				<div class="controls">
					<input type="number" id="CollectiblesUserEditionSize" name="edition_size">
				</div>
			</div>		
		{/eq}
		<div class="control-group">
			<label class="control-label" for="CollectiblesUserArtistProof">Artist's Proof</label>
			<div class="controls">
				<input type="checkbox" id="CollectiblesUserArtistProof" value="1" name="artist_proof">
			</div>
		</div>				
		<div class="control-group">
			<label class="control-label" for="dialogCost">How much did you pay? (Retail:$600.00)</label>
			<div class="controls">
				<input type="number" maxlength="23" step="any" id="dialogCost" name="cost">
			</div>
		</div>					
		<div class="control-group">
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
			</div>
		</div>	
		<div class="control-group">
			<label class="control-label" for="CollectiblesUserMerchantId">Where did you purchase the collectible?</label>
			<div class="controls">
				<input type="text" id="CollectiblesUserMerchantId" maxlength="150" name="merchant" autocomplete="off">
			</div>
		</div>			
		<div class="control-group">
			<label class="control-label" for="CollectiblesUserPurchaseDate">When did you purchase this collectible?</label>
			<div class="controls">
				<input type="text" id="CollectiblesUserPurchaseDate" maxlength="8" name="purchase_date">
			</div>
		</div>			
	</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">
			Close
		</button>
		<button class="btn btn-primary save" autocomplete="off">
			Save
		</button>
	</div>
</div>


