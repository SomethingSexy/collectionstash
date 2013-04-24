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
		<h2>Collection Stash Documentation</h2>
		<p>
			The following guide should help you with any questions you might have when submitting and adding collectibles to Collection Stash
		</p>

		<h3 id="overview">What is a Collectible?</h3>
		<p>
			A collectible here at Collection Stash can really be anything that you as a member might collect.  These can range from mass-produced action figures or toys to limited statues or rare customs.  These can be made in quantities or built individually by you.  We want you to be able to share and catalog your passions and show us everything you collect.
			Admittedly we do not support all types of collectibles yet, we are striving for that!
		</p>
		<p>
			Most collectibles describe the physical product that is sold, but they do not have to! This is the case when you made the collectible yourself.  Each product is made up of parts, when these parts are combined together they make a collectible that you can add to your stash.  Some might have one part, others might have hundreds.
		</p>

		<h3 id="submit-new">Submitting a New Collectible</h3>

		<p>
			When submitting a new collectible you are presented with some options.  It is important to pick the right type of collectible, depending on what you are trying to add.
		</p>

		<img src="/img/documentation/submit_new_collectible_options.png" class="img-polaroid">

		<h4 id="types">Types</h4>
		<p>
			The following three types should accurately describe every type of collectible you could own (hopefully!).  When adding a new collectible please try and find the type that best describes what you are adding.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Mass-Produced
			</dt>
			<dd>
				This your standard collectible that is made and sold in mass quantity.  This type handles both official (licensed) and unofficial (customs) collectibles.  The main key is that is must be something <strong>sold in mass quantity</strong>, whether that is off the shelf, online or from an artist.
			</dd>
			<dt>
				Original
			</dt>
			<dd>
				An original collectible is ones that is <strong>owned by you</strong> but made by someone else.  Whether you comissioned personally or bought from someone where <strong>only one must exist</strong>.
			</dd>
			<dt>
				Custom
			</dt>
			<dd>
				A custom collectible is one that you built or bashed yourself.  This could be anything that you made completely from scratch or you pieced together from various custom or mass-produced parts.
			</dd>
		</dl>

		<h4 id="status">Status</h4>
		<p>
			A collectible has a corresponding status.  That status might be different depending on the type of collectible it is and how far it has gone through the approval process (if applicable).
		</p>
		<p>
			Regardless of type, all collectibles start in the draft status.  Once you get to the collectible form, after selecting a platform you will see this indicated at the top.  The status bar will be visible on all collectibles that are in draft and submitted status.
		</p>
		<img src="/img/documentation/add_new_draft_status.png" class="img-polaroid">
		<p></p>
		<p>
			A collectible can be in the following statuses.
		</p>
		<dl class="dl-horizontal">
			<dt>
				Draft
			</dt>
			<dd>
				This is a collectible you are working on.  It cannot be viewed by the public and it has not been submitted for approval if it is a mass-produced collectible.
			</dd>
			<dt>
				Submitted
			</dt>
			<dd>
				This a collectible you have finished creating and are satisfied enough to submit it for approval.  When it is in a submitted status you cannot edit it.  However, if you need to make a change before it is approved you can turn it back to a Draft status and make your changes.  Collectibles in this status can be viewed by members but cannot be edited.
			</dd>
			<dt>
				Active
			</dt>
			<dd>
				This a collectible that was either approved by an admin (applies to mass-produced collectibles only) or it was actived by the user (applies to customs and originals).  It can be viewed by members and non-members.
			</dd>
		</dl>
		<h4 id="platforms">Platform</h4>
		<p>
			Besides being of a specific type, each collectible belongs to a platform.  The platform describes the collectible category.  Ideally this could be anything but right now we have a predefined list of platforms for you to choose from.
		</p>

		<h4 id="manufacturers">Manufacturers</h4>
		<p>
			Mass-produced and original collectibles can specify a manufacturer or producer.  This is the company or business that made or produced the collectible.  In most cases, this is the place you bought it from or the name that is on the box!  Each manufacturer is linked to platforms and brands.  If you do not see what you are looking for you can edit them here.
		</p>
		<p>
			Please note, a manufacturer is not required.  Some collectibles might not be made or produced by a company but instead are designed and produced by an individual person.  If that is the case, you will want to indicate the <a href="#artists">artist</a> instead.
		</p>
		<p>
			You can add and edit manufacturers directly from the collectible form. </a>
		</p>

		<h5>Manufacturer Information</h5>
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

		<h5>Adding New Manufacturers</h5>
		<p>
			Coming soon!
		</p>
		<h5>Editing Existing Manufacturers</h5>
		<p>
			Coming soon!
		</p>
		<h4 id="artists">Artists</h4>
		<p>
			Another major piece of information a collectible might have is an artist.  Every type of collectible can have artists and it is important to document and give credit to those people who worked to make that product.
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
		<h5>Manufacturers that are also considered Artists</h5>
		<p>
			In some cases, a manufacturer might be an artist on a collectible.  Ideally we would directly link that manufacturer to collectible but until that is supported, please add the manufacturer as an artist.
		</p>
		<p>
			Here is an example of <a href="http://www.collectionstash.com/artist/155/gentle-giant">Gentle Giant</a> being the sculptor for a <a href="http://www.collectionstash.com/collectibles/view/3683/diamond-select-toys-howard-the-duck-statue">statue</a> that was made by <a href="http://www.collectionstash.com/manufacturer/24/diamond-select-toys">Diamnod Select Toys.</a>
		</p>

		<h4 id="photos">Photos</h4>
		<p>
			Coming soon!
		</p>
		<h4 id="parts">Parts</h4>
		<p>
			Parts are all of the pieces that make up a collectible.  They can be anything and like a collector we like to know how our collectible is made and everything it contains.  This is important for keeping documentation on a collectible but it is also important for customizing!
		</p>
		<p>
			A part should be added for each individual piece of the collectible.  If there are multiple of the same part, then only <strong>one</strong> should be added but the count should be adjusted.
		</p>
		<p>
			Parts can also be shared amongst collectibles.  This feature allows us to indicate which part might be used across multiple different collectibles.  This also allows customers to build bashes and collectibles from other parts that were made.
		</p>

		<h5>Adding New Parts to a Collectible</h5>
		<p>
			New parts are displayed and added near the bottom of the collectible form.
		</p>
		<img src="/img/documentation/part_add_new_dropdown.png" class="img-polaroid">
		<p></p>
		<p>
			When selected you will be presented with a modal dialog where you can fill out information about the part, this includes a category,name, description, manufacturer, artist, scale and count.
		</p>
		<img src="/img/documentation/part_add_new_modal.png" class="img-polaroid">
		<p></p>
		<p>
			Below is a more indepth description of the fields that can be filled out for each part.
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
			When a part is added to a collectible it also has the following attributes.  These attributes are tied to the relationship between the collectible and part, not to the part itself.
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

		<h5>Adding Existing Parts to a Collectible</h5>
		<p>
			You can also add parts from other collectibles to the current collectible you are adding.  This is especially helpful when we are trying to see what part might be shared across multiple collectibles.
		</p>
		<p>
			To add an existing part to this collectible use the following option.
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
		<p>Once you select the part you want, you will be allowed to add the count.  Click "Add" to add it to your collectible.</p>
		<img src="/img/documentation/part_add_existing_modal_selected.png" class="img-polaroid">
		<p></p>		
		

		<h5>Adding Photos</h5>
		<p>
			After you add the part to the collectible, you can upload photos for that part.
		</p>
		<h5>Editing</h5>
		<h5>Removongg Dupilicate Parts</h5>
		<p>
			Coming soon!
		</p>
		<h5>Removing Parts</h5>
		<p>
			Coming soon!
		</p>
		<h5>When should I add a part vs making a new collectible?</h5>
		<p></p>
		<h5>Adding Parts to Original and Custom Collectibles</h5>

		<h4 id="sumbit"> Submitting/Activating a Collectible </h4>

		<h4 id="delete"> Deleting a Collectible </h4>

	</div>

</div>

