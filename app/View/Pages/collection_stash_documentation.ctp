<?php $this -> set("title_for_layout", __('A Guide to Collection Stash')); ?>
<div class="row">
	<div class="span3">
		<ul class="nav nav-list">
			<li>
				<a href="#overview"> <i class="icon-chevron-right"></i> What is a Collectible? </a>
			</li>
			<li>
				<a href="#submit-new"> <i class="icon-chevron-right"></i> Submitting a New Collectible </a>
				<ul class="nav nav-list">
					<li>
						<a href="#types"> <i class="icon-chevron-right"></i> Types </a>
					</li>
					<li>
						<a href="#platforms"> <i class="icon-chevron-right"></i> Platforms </a>
					</li>
					<li>
						<a href="#status"> <i class="icon-chevron-right"></i> Status </a>
					</li>
					<li>
						<a href="#manufacturers"> <i class="icon-chevron-right"></i> Manufacturers </a>
					</li>
					<li>
						<a href="#artists"> <i class="icon-chevron-right"></i> Artists </a>
					</li>
					<li>
						<a href="#photos"> <i class="icon-chevron-right"></i> Photos </a>
					</li>
					<li>
						<a href="#parts"> <i class="icon-chevron-right"></i> Parts </a>
						<ul class="nav nav-list">
							<li>
								<a href="#parts-adding-new-parts"> <i class="icon-chevron-right"></i> Adding New Parts to a Collectible </a>
							</li>
							<li>
								<a href="#parts-add-existing"> <i class="icon-chevron-right"></i> Adding Existing Parts to a Collectible </a>
							</li>
							<li>
								<a href="#parts-photos"> <i class="icon-chevron-right"></i> Adding Photos </a>
							</li>
							<li>
								<a href="#parts-when"> <i class="icon-chevron-right"></i> When should I add a new part vs adding an existing part? </a>
							</li>
							<li>
								<a href="#parts-customs"> <i class="icon-chevron-right"></i> Adding Parts to Custom Collectibles </a>
							</li>
							<li>
								<a href="#parts-original"> <i class="icon-chevron-right"></i> Adding Parts to Original Collectibles </a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#submit"> <i class="icon-chevron-right"></i> Submitting/Activating a Collectible </a>
					</li>
					<li>
						<a href="#delete"> <i class="icon-chevron-right"></i> Deleting a Collectible </a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="span9">
		<h1>Collection Stash Documentation</h1>
		<p>
			The following guide will help you with any questions you may have when submitting and adding collectibles to Collection Stash.
		</p>

		<h2 id="overview">What is a Collectible?</h2>
		<p>
			A collectible here at Collection Stash can be anything that you, as a member, might collect.  These can range from mass-produced action figures or toys to limited statues or rare customs.  These can be made in quantities or built individually by you.  We want you to be able to catalog your passions and share everything you collect.
			Admittedly we do not yet support all types of collectibles; we are striving for that!
		</p>
		<p>
			Most collectibles in Collection Stash describe the physical product that is sold, but they do not have to! This is the case when you made the collectible yourself.  Each product is made up of parts and when these parts are combined together they make a collectible that you can add to your stash.  Some products might have one part while other products might have hundreds.
		</p>

		<h2 id="submit-new">Submitting a New Collectible</h2>

		<p>
			When submitting a new collectible, various types of collectibles are presented.  It is important to pick the right type based on what you are trying to add.
		</p>

		<img src="/img/documentation/submit_new_collectible_options.png" class="img-polaroid">

		<h3 id="types">Types</h3>
		<p>
			The following three types should accurately describe every type of collectible you could own (hopefully!).  When adding a new collectible please try to find the type that best describes the item you are adding.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Mass-Produced
			</dt>
			<dd>
				This your standard collectible that is made and sold in mass quantity.  This type handles both official (licensed) and unofficial (customs) collectibles.  The main key for this type is that it must be something <strong>sold in mass quantity</strong>; whether that is off the shelf, online, or from an artist.
			</dd>
			<dt>
				Original
			</dt>
			<dd>
				An original collectible is one that is <strong>owned by you</strong> but was made by someone else.  Whether you commissioned the item personally or purchased it from someone, <strong>only one must exist</strong>.
			</dd>
			<dt>
				Custom
			</dt>
			<dd>
				A custom collectible is one that you built or bashed yourself.  This can be anything that you made from scratch or you pieced together using various custom or mass-produced parts.
			</dd>
		</dl>
		<h3 id="platforms">Platform</h3>
		<p>
			In addition to having a specific type, each collectible belongs to a platform.  The platform describes the collectible category.  Currently we have a predefined list of platforms for you to choose from:
		</p>
		<ul>
			<li>
				Action Figure
			</li>
			<li>
				Action Figure Environment
			</li>
			<li>
				Action Figure Accessory
			</li>
			<li>
				Diorama
			</li>
			</li>
			<li>
				Bust
			</li>
			<li>
				Maquette
			</li>
			<li>
				Ornament
			</li>
			<li>
				Statue
			</li>

			<li>
				Statue Accessory
			</li>

			<li>
				Print
			</li>
			<li>
				Replica
			</li>

			<li>
				Prop Replica
			</li>

			<li>
				Vinyl Figure
			</li>
			<li>
				Designer Toy
			</li>
			<li>
				Coin
			</li>
		</ul>
		<h3 id="status">Status</h3>
		<p>
			Each collectible has a corresponding status.  The status may differ depending on the type of collectible and how far it is in the approval process (if applicable).
		</p>
		<p>
			Regardless of type, all collectibles begin in the Draft status.  When you are on the collectible edit page, after selecting a platform you will see the Draft status indicated at the top.  The status bar is visible on all collectibles that are in Draft and Submitted status.
		</p>
		<img src="/img/documentation/add_new_draft_status.png" class="img-polaroid">
		<p></p>
		<p>
			A collectible can be in one of the following statuses:
		</p>
		<dl class="dl-horizontal">
			<dt>
				Draft
			</dt>
			<dd>
				This is a collectible you are working on adding to Collection Stash.  It cannot be viewed by the public and, if it is a mass-produced collectible, it has not been submitted for approval.
			</dd>
			<dt>
				Submitted
			</dt>
			<dd>
				This is a collectible you have finished creating and are ready to submit for approval.  When the collectible is in a Submitted status, it cannot be edited.  If a change is needed before it is approved you can set it back to a Draft status and make your changes.  Collectibles in Submitted status can be viewed by members.
			</dd>
			<dt>
				Active
			</dt>
			<dd>
				This is a collectible that was either approved by an admin (applies to mass-produced collectibles only) or was activated by the user (applies to customs and originals).  It can be viewed by members and non-members.
			</dd>
		</dl>

		<h3 id="manufacturers">Manufacturers</h3>
		<p>
			Mass-produced and original collectibles can have a manufacturer or producer specified.  This is the company or business that made or produced the collectible.  In most cases this is the place you where you purchased the item, or the name that is on the box!  Each manufacturer is linked to platforms and brands.
		</p>
		<p>
			Please note, a manufacturer is not required.  Some collectibles may not be manufactured or produced by a company but instead are designed and produced by an individual person.  If that is the case, you will want to indicate the <a href="#artists">artist</a> instead.
		</p>
		<p>
			You can add and edit manufacturers directly from the collectible form. </a>
		</p>

		<h4>Manufacturer Information</h4>
		<p>
			The following pieces of information are directly linked to a manufacturer.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Brands
			</dt>
			<dd>

			</dd>
			<dt>
				Categories
			</dt>
			<dd>

			</dd>
			<dt>
				Platforms
			</dt>
			<dd>

			</dd>
		</dl>

		<h4>Adding New Manufacturers</h4>
		<p>
			Coming soon!
		</p>
		<h4>Editing Existing Manufacturers</h4>
		<p>
			Coming soon!
		</p>
		<h3 id="artists">Artists</h3>
		<p>
			Another major piece of information a collectible might have is an artist.  Every type of collectible can have an artist (or artists) and it is important to document and give credit to those who worked to make the product.
		</p>
		<p>
			It is especially important to indicate an artist when there is no manufacturer and this collectible might have been made by one person.  A great example of this are mass-produced custom collectibles and prints.
		</p>

		<p>
			The artist section can be found near the top of the collectible form. <strong>The first one you add will be the primary artist.</strong>
		</p>
		<img src="/img/documentation/artist_form.png" class="img-polaroid">
		<p></p>
		<p>
			This is a typeahead input field.  Please use the full name or most common handle for that artist.  If the artist exsists already it will show up in a drop-down box for you to select.
		</p>
		<img src="/img/documentation/artist_form_search.png" class="img-polaroid">
		<p></p>
		<p>
			Here is an example of what it should look like when an artist has been added.
		</p>
		<img src="/img/documentation/artist_form_add.png" class="img-polaroid">
		<h4>Manufacturers that are also considered Artists</h4>
		<p>
			In some cases, a manufacturer might be an artist on a collectible.  In the future we plan to directly link that manufacturer to the collectible but until that is supported please add the manufacturer as an artist.
		</p>
		<p>
			Here is an example of <a href="http://www.collectionstash.com/artist/155/gentle-giant">Gentle Giant</a> being the sculptor for a <a href="http://www.collectionstash.com/collectibles/view/3683/diamond-select-toys-howard-the-duck-statue">statue</a> that was made by <a href="http://www.collectionstash.com/manufacturer/24/diamond-select-toys">Diamond Select Toys.</a>
		</p>

		<h3 id="photos">Photos</h3>
		<p>
			Coming soon!
		</p>
		<h3 id="parts">Parts</h3>
		<p>
			Parts are all of the pieces that make up a collectible.  Like all collectors we like to know how our collectible is made and everything it contains.  It is important for keeping documentation on a collectible but it is also important for customizing!
		</p>
		<p>
			A part should be added for each individual piece of the collectible.  If there are multiple of the same part, then only <strong>one</strong> should be added but the count should be adjusted.
		</p>
		<p>
			Parts can also be shared amongst collectibles.  This allows for the ability to indicate which part might be used across multiple different collectibles.  This also allows customers to build bashes and collectibles from other parts that were made.
		</p>

		<h4 id="parts-adding-new-parts">Adding New Parts to a Collectible</h4>
		<p>
			New parts are displayed and added near the bottom of the collectible form.
		</p>
		<img src="/img/documentation/part_add_new_dropdown.png" class="img-polaroid">
		<p></p>
		<p>
			When selected you will be presented with a modal dialog where you can fill out information about the part including: category, name, description, manufacturer, artist, scale and count.
		</p>
		<img src="/img/documentation/part_add_new_modal.png" class="img-polaroid">
		<p></p>
		<p>
			Below is a more in-depth description of the fields that can be populated for each part.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Category
			</dt>
			<dd>
				The part is required to be in a specific category.  Please find the category that best fits the part you are trying to add.  If you cannot find an appropriate category you can use the generic "Part" category.
			</dd>
			<dt>
				Name
			</dt>
			<dd>
				A short title for the part.  This is required.
			</dd>
			<dt>
				Description
			</dt>
			<dd>
				More detailed information about the part.  This is required.
			</dd>
			<dt>
				Manufacturer
			</dt>
			<dd>
				This indicates what manufacturer made the part.  This is optional.
			</dd>
			<dt>
				Artist
			</dt>
			<dd>
				This indicates what artist made the part.  This is optional.
			</dd>
			<dt>
				Scale
			</dt>
			<dd>
				This indicates the scale of the part relative to the scale of the collectible.  This is optional.
			</dd>
		</dl>

		<p>
			When a part is added to a collectible, another attribute is included in the relationship between the collectible and the part.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Count
			</dt>
			<dd>
				The total number of this part the collectible has.
			</dd>
		</dl>
		<p>
			Here is an example of a part that was successfully added to a new collectible.
		</p>
		<img src="/img/documentation/part_add_new_added.png" class="img-polaroid">

		<h4 id="parts-add-existing">Adding Existing Parts to a Collectible</h4>
		<p>
			You can also add parts from other collectibles to the current collectible being added.  This is especially helpful when trying to see if a part might be shared across multiple collectibles.
		</p>
		<p>
			To add an existing part to a collectible use the following option under Action:
		</p>
		<img src="/img/documentation/part_add_existing_dropdown.png" class="img-polaroid">
		<p></p>
		<p>
			When selected you will be presented with a modal dialog where you can search for parts either by collectible or by part.
		</p>
		<img src="/img/documentation/part_add_existing_modal.png" class="img-polaroid">
		<p></p>
		<p>
			If you know the collectible that the part you want to add is attached to, then click "Find By Collectible".  You will be presented with a screen that will allow you to search for a collectible.  When you find the part you wish to add, click it.
		</p>
		<img src="/img/documentation/part_add_existing_modal_search.png" class="img-polaroid">
		<p></p>
		<p>
			Once you select the part you want, you will be allowed to add the count.  Click "Add" to add it to your collectible.
		</p>
		<img src="/img/documentation/part_add_existing_modal_selected.png" class="img-polaroid">
		<p></p>
		<p>
			The same process can be applied when searching for a part directly.
		</p>
		<h4 id="parts-photos">Adding Photos</h4>
		<p>
			After you add the part to the collectible, you can upload photos for that part by clicking the "Edit Part Photos" link as shown below.
		</p>
		<img src="/img/documentation/part_edit_photos.png" class="img-polaroid">
		<p></p>
		<h4>Editing</h4>
		<p>
			Coming soon!
		</p>
		<h4>Removing Dupilicate Parts</h4>
		<p>
			Coming soon!
		</p>
		<h4>Removing Parts</h4>
		<p>
			Coming soon!
		</p>
		<h4 id="parts-when">When should I add a new part vs adding an existing part?</h4>
		<p>
			When adding parts, consideration should be given to if it should be added as a new part or linked from an existing part.  If you know that the part you are adding is shared with another collectible, please search for that collectible first to see if it has already been added.  Otherwise, add it as a new part but make sure to update any collectibles that might also have this part.
		</p>

		<h4 id="parts-customs">Adding Parts to Custom Collectibles</h4>
		<p>
			Customs are a unique collectible type because they are built or pieced together by you.  Because of this, we need to also indicate that with any <strong>new</strong> parts that are being added to a custom.  It is <strong>strongly</strong> encouraged to add existing parts to a custom when applicable.
		</p>
		<p>
			If you are bashing together a custom from other parts, some of which might <strong>be mass-produced</strong> then you <strong>need</strong> to add that part as a <strong>mass-produced collectible</strong> first.  After that has been done, the part can be linked to your custom. <a href="http://www.collectionstash.com/collectibles/view/3795">Here</a> is a great example of how that is done.
		</p>
		<p>
			If the part you are adding was made by you, is a 1 for 1 commission from an artist, or is generic (not sure where it came from) then you can add it new, directly to the custom collectible.
		</p>
		<p>
			You will be prompted to choose between three options when adding a new part to a collectible.  Any new part added to a custom will <strong>not</strong> be allowed to be shared/linked to other collectibles.  It is very important you only add new parts to customs that fall within these three options. <img src="/img/documentation/part_add_custom_modal.png" class="img-polaroid">
		</p>
		<p></p>
		<p>
			When adding existing parts to a custom you can also indicate a status.  These statuses can help you keep organized with what parts you already own, what parts you have ordered, and what parts you might need. Currently, this field is only applicable when you are adding an existing part to a collectible.  You can add the following statues any part added to a collectible.
		</p>
		<ul>
			<li>
				Owned
			</li>
			<li>
				Preordered
			</li>
			<li>
				Wanted
			</li>
		</ul>

		<p>
			Status is indicated in the upper-right of the part card.
		</p>
		<img src="/img/documentation/part_card.png" class="img-polaroid">
		<p></p>
		<p>The status field can be selected when you add an existing part to a custom collectible using the Add Existing Part menu option.</p>
		<img src="/img/documentation/part_add_existing_custom_modal.png" class="img-polaroid">
		<p></p>
		<p></p>
		<h4 id="parts-original">Adding Parts to Original Collectibles</h4>
		<p>
			All parts added directly to an original collectible will be automatically labeled as "original" parts.  They cannot be shared/linked to other collectibles at this time.
		</p>

		<h3 id="sumbit"> Submitting/Activating a Collectible </h3>
		<p>
			Depending on the type of collectible being added, when you are finished you may be submitting the collectible for approval or instantly activating it.
		</p>
		<p>
			If the type of collectible is <strong>mass-produced</strong> it will be submitted for approval and await approval by an admin.  If the collectible details are found to be accurate and it the collectible is not a duplicate within Collection Stash it will be approved and moved to the Active status.
		</p>
		<p>
			If the type of collectible is a <strong>custom or original</strong> you can activate it immediately without approval.
		</p>
		<p>
			To submit a mass-produced collectible for approval click the "Submit for approval" button near the top of the page.
		</p>
		<img src="/img/documentation/submit_approval.png" class="img-polaroid">
		<p></p>
		<p>
			When you click the button, the collectible will go through the validation process.  If any errors are found they will be indicated at the top of the page below the status section.  We also do smart checking for duplicates.  If potential duplicates are found they will be indicated.
		</p>
		<img src="/img/documentation/dup_check.png" class="img-polaroid">
		<p></p>
		<p>
			This is an example of a duplication check when trying to submit a collectible called "Stormtroooper" for Sideshow Collectibles.
		</p>
		<h3 id="delete"> Deleting a Collectible </h3>
		<p>
			Coming soon!
		</p>
	</div>

</div>

