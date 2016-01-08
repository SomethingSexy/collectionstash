<div class="col-md-12">
    <h2>Change Log</h2>
    <h3>Release Notes for v4.2.1</h3>
    <ul>
        <li>Fixed an issue with company listing page where filtering would stop paging from working. (#242)</li>
        <li>Fixed an issue with company listing page where filtering would stop the edit modal from working. (#241)</li>
        <li>Updated the user gallery to match stash photo view. (#243)</li>
    </ul>
    <h3>Release Notes for v4.2.0</h3>
    <ul>
        <li>Updated the manufacturer listing page to include a text search.</li>
        <li>Updated the collectible cards on the manufacturer detail page to match the other parts of the site.</li>
        <li>Updated the user stash value processing so that if a collectible doesn't have a listing value, it uses the retail value.</li>
        <li>Updated the stash sale view and history view to reflect the change above.</li>
    </ul>
    <h3>Release Notes for v4.1.2</h3>
    <ul>
        <li>Fixed an issue with the collectible detail page not showing up when you are anonymous.</li>
        <li>Updated the stash favorite link so it highlights when active.</li>
        <li>Updated the stash favorite icon so it only shows up when you are logged in.</li>
    </ul>   
    <h3>Release Notes for v4.1.1</h3>
    <ul>
        <li>Fixing an issue with marking a collectible as sold. (#231)</li>
    </ul>
    <h3>Release Notes for v4.1.0</h3>
    <ul>
        <li>Revamped the favorite/subscription system. (#60)</li>
        <li>UI updates. (#227)</li>
    </ul>
    <h3>Release Notes for v4.0.1</h3>
    <ul>
        <li>Fixed issue with sideshow parsing images.</li>
        <li>Fixed issue with sideshow parsing description.</li>
    </ul>
    <h3>Release Notes for v4.0.0</h3>
    <ul>
        <li>Allowing users to import collectible data via manufacturer url.  The first supported manufacturer is Sideshow Collectibles. (#222)</li>
        <li>Moved the collectible type selection to the collectible edit page.  You can now change type like any other field. (#197)</li>
        <li>Increased sized of description field for a collectible. </li>
    </ul>
    <h3>Release Notes for v3.12.0</h3>
    <ul>
        <li>Added more links to see pending collectibles. (#218)</li>
        <li>Updated admin approval UI. (#220)</li>
        <li>Adding support for composer. (#221)</li>
        <li>Fixed an issue where users couldn't register. (#223)</li>
        <li>Fixed an issue with newly registered users not having wishlists. (#224)</li>
    </ul>
    <h3>Release Notes for v3.11.3</h3>
    <ul>
        <li>Fixed an issue with the point system. (#217)</li>
        <li>Fixed an issue where the add to wish list button on the collectible detail page would an empty history point. (#214)</li>
        <li>Removed the additional link on the wish list page.  It didn't make sense. (#213)</li>
    </ul>
    <h3>Release Notes for v3.11.2</h3>
    <ul>
        <li>Fixed an issue with the show more link not working when trying to link a photo to a collctible in your stash. (#212)</li>
        <li>Fixed an issue with the brand dropdown not refreshing aftering adding a new one to a company. (#178)</li>
    </ul>
    <h3>Release Notes for v3.11.1</h3>
    <ul>
        <li>Fixed an issue with empty edits. (#145)</li>
        <li>Fixed an issue with delete part not working. (#210)</li>
        <li>Fixed an issue the part photo not indicating if a photo is already peneding removal. (#211)</li>
    </ul>
    <h3>Release Notes for v3.11.0</h3>
    <ul>
        <li>Updated the name field so it propertly validates the length. (#209)</li>
        <li>Fixed the manufacturer sorting on the stash history page. (#208)</li>
        <li>Updated the notifications page to utilize Backbone.Pageable. (#207)</li>
        <li>Now when you click on links on the company search page, it will show an image gallery. (#206)</li>
        <li>Fixed an issue with ZeroClipboard. (#205)</li>
        <li>Persisting filters when switching between the search list and search tiles. (#199)</li>
        <li>Fixed an issue when trying to add a collectible from the search page with a ' in the description. (#198)</li>
        <li>Updated the sorting on the stash history page to indicate direction. (#196)</li>
        <li>Added the manufacturer name preceding the collectible name. (#192)</li>
        <li>Fixed an issue with the 'Why do my values look werid' popup. (#191)</li>
        <li>On the submit collectible page, updated the length to be labeled height.  This aligns with the collectible detail page. (#190)</li>
        <li>Removed the drop down menu on the collectible listing page.  Added links right on the page. (#189)</li>
        <li>Alphabetically sorted the category listing. (#187)</li>
        <li>Added more descriptive error handling on the collectible edit page. (#186)</li>
        <li>Updated all scripts to pull from bower. (#185, #184)</li>
        <li>Updated the user gallery page. (#183)</li>
        <li>Fixed an issue the merchant field on the add to stash modal. (#120)</li>
        <li>Updated to support EAN-13 UPC. (#114)</li>
    </ul>
    <h3>Release Notes for v3.10.1</h3>
    <ul>
        <li>Updated the part upload to not allow the user to delete images that are already pending a delete. (#145)</li>
        <li>Removed unnecessary admin views. (#180)</li>
    </ul>
    <h3>Release Notes for v3.10.0</h3>
    <ul>
        <li>Fixed issue with uploading image via url. (#168)</li>
        <li>Added collectible view tracking. (#171)</li>
    </ul>
    <h3>Release Notes for v3.9.1</h3>
    <ul>
        <li>Fixed an issue where the url was not showing up when editing a company. (#177)</li>
        <li>Fixed validation issues with the bio field. (#176)</li>
    </ul>
    <h3>Release Notes for v.3.9.0</h3>
    <ul>
        <li>Updated the add to stash modal so it indicatesif you already own the collectible. (#117)</li>
        <li>Updated the collectible detail page to show photos that users linked from their stash. (#163)</li>
        <li>Added a company listing page under "Collectibles Catalog". (#154)</li>
        <li>Added a bio field for a company. (#155)</li>
        <li>Added a logo field for a company. (#156)</li>
    </ul>
    <h3>Release Notes for v3.8.3</h3>
    <ul>
        <li>Fixed an escaping issue with collectible description. </li>
    </ul>
    <h3>Release Notes for v3.8.2</h3>
    <ul>
        <li>Activity was not being generated when a user updated a collectible in their stash. (#13)</li>
        <li>Linking photos to collectibles in your stash was missing from the stash table view.</li>
    </ul>
    <h3>Release Notes for v3.8.1</h3>
    <ul>
        <li>Fixed an issue that would cause the stash photo page to go to the last "page" view if you clicked a different tab and then went back to the photo tab.</li>
    </ul>
    <h3>Release Notes for v3.8.0</h3>
    <ul>
        <li>Added a new feature allowing users the ability to link photos to collectibles in their stash.  When a photo is linked, it will show up as the image in the stash instead of the default. (#162)</li>
    </ul>
    <h3>Release Notes for v3.7.0</h3>
    <ul>
        <li>Updated stash and wishlist images so they scale better for the size. (#131)</li>
        <li>Fixed an issue with the collectible search gallery images not being the correct size. (#161)</li>
        <li>Fixed an issue with the name of the collectible on the collectible search tile page not escaping correctly. (#160)</li>
    </ul>
    <h3>Release Notes for v3.6.1</h3>
    <ul>
        <li>Fixed an issue where saving an invalid collectible would not reset the save button. (#158)</li>
        <li>Fixed an issue where copy curly quotes from an outside source would make the collectible invalid for the description field. (#159)</li>
    </ul>
    <h3>Release Notes for v3.6.0</h3>
    <ul>
        <li>Updated part view to show the image and collectibles linked to the part. (#150)</li>
    </ul>
    <h3>Release Notes for v3.5.2</h3>
    <ul>
        <li>Fixed an issue where you could not edit a collectible name that contained a ". (#151)</li>
        <li>Fixed an issue where old or missing eBay transactions were not deleting. (#148)</li>
    </ul>
    <h3>Release Notes for v3.5.1</h3>
    <ul>
        <li>Fixed an issue where you could not add new brands to a manufacturer. (#147)</li>
    </ul>
    <h3>Release Notes for v3.5.0</h3>
    <ul>
        <li>Fixed an issue where image gallery was not working for parts on the collectible edit page. (#135)</li>
        <li>Removed the hard link between a collectible type and manufacturer.  You can now added any manufacturer to any collectible type. (#137)</li>
        <li>Removed manufacturer specialzied type logic.  This was removed from the UI previously but all of the code was still in there. (#138)</li>
        <li>Fixed an issue where new SPA pages were sometimes timing out when a user had a fresh cache. (#139)</li>
        <li>Added a loading indicator to the "more" button on stash/wishlist tile views. (#140)</li>
        <li>Fixed an issue when loading a user's stash and the activity isn't returned the page would fail to load. (#142)</li>
        <li>Fixed an issue where the "more" button was still visible when a user's stash was filtered to less than 25 collectibles. (#144)</li>
        <li>Fixed an issue where the activity text for approving a new part was incorrect. (#146)</li>
    </ul>
    <h3>Release Notes for v3.4.0</h3>
    <ul>
        <li>Added loading indicators when switching between tabs that require data fetching on the user stash/profile page. (#134)</li>
    </ul>
    <h3>Release Notes for v3.3.2</h3>
    <ul>
        <li>eBay transactions were not working because auth_token was expired.  Generated a new token (#136).</li>
    </ul>
    <h3>Release Notes for v3.3.1</h3>
    <ul>
        <li>Fixed an issue where the photo share modal was not displaying (#133).</li>
        <li>Fixed an issue where stash fact information was not accurate (#132).</li>
    </ul>
    <h3>Release Notes for v3.3.0</h3>
    <ul>
        <li>Fixed an issue when registering it would display an error message. (#128)</li>
        <li>Allowing part photos to be automatically added and removed.  This is start of opening up edits to be automatic. (#126)</li>
        <li>Users can now see the parts they have added to the collectible if they were submitted for approval. (#125)</li>
        <li>Updated the comment modal so it focuses the text area. (#118)</li>
        <li>Refactored collectible part edit. (#111)</li>
        <li>Refactored collectible submit/edit to support requirejs. (#64)</li>
        <li>Updated artsits, tags, and retailers to use select2.  This should fix a Mac OS/Safari issue. (#57)</li>
        <li>Random bug fixes.</li>
    </ul>
    <h3>Release Notes for v3.2.3</h3>
    <ul>
        <li>Fixed an issue with our new SPA pages and IE9. (#119).</li>
    </ul>
    <h3>Release Notes for v3.2.2</h3>
    <ul>
        <li>Fixed an issue where users could not edit the merchant field for collectibles in their stash. (#112).</li>
        <li>Fixed an issue where switching between the tile and list view was broken on the stash page if your stash had less than 25 collectibles. (#113)</li>
    </ul>
    <h3>Release Notes for v3.2.1</h3>
    <ul>
        <li>Fixed an issue where users could not edit comments on a collectible page. (#109).</li>
    </ul>
    <h3>Release Notes for v3.2.0</h3>
    <ul>
        <li>Fixed an issue with the images on the photo sharing page. (#105).</li>
        <li>Fixed the stash page so when collectibles are removed the tiles realign. (#99).</li>
        <li>Updated the login page so that users can login via email as well.</li>
    </ul>
    <h3>Release Notes for v3.1.0</h3>
    <ul>
        <li>Fixed an issue where editing comments with new lines would not render correctly. (#104)</li>
        <li>Added a sort for comments. (#100)</li>
    </ul>
    <h3>Release Notes for v3.0.3</h3>
    <ul>
        <li>Fixed an issue where - were not allowed in collectible description. (#103)</li>
        <li>Fixed an issue where comments were not rendering new lines correctly. (#102)</li>
    </ul>
    <h3>Release Notes for v3.0.2</h3>
    <ul>
        <li>Fixed issue where you could not submit a new collectible because description kept being invalid. (#101)</li>
    </ul>
    <h3>Release Notes for v3.0.1</h3>
    <ul>
        <li>Fixed issue where you could not remove collectibles from wishlist. (#98)</li>
        <li>Fixed issue where you could not add collectibles to your wishlist. (#97)</li>
    </ul>
    <h3>Release Notes for v3.0.0</h3>
    <h3>Release Notes for v2.18</h3>
    <ul>
        <li>Added filters to the user stash page.</li>
    </ul>
    <h3>Release Notes for v2.17</h3>
    <ul>
        <li>Updated the UI for filtering on the collectible catalog.</li>
        <li>Added filters for pending collectibles, variants, and scales.</li>
        <li>Updated the version of Twitter typeahead</li>
    </ul>
    <h3>Release Notes for v2.16</h3>
    <ul>
        <li>Added the ability for an admin to delete a collectible and replace a duplicate collectible.</li>
        <li>Fixed some UI issues with the collectible search page.</li>
    </ul>
    <h3>Release Notes for v2.15.3</h3>
    <ul>
        <li>Fixed an issue where an admin could not automatically delete photos.</li>
    </ul>
    <h3>Release Notes for v2.15.2</h3>
    <ul>
        <li>Fixed issue where a user with a huge stash would view their comments and the response would blow up.</li>
    </ul>
    <h3>Release Notes for v2.15.1</h3>
    <ul>
        <li>Fixed an issue with deleting pending collectible uploads.</li>
    </ul>
    <h3>Release Notes for v2.15</h3>
    <ul>
        <li>Updated collectible detail transaction view so that the data is rendered via cake instead of dust.  This should make the page load faster and help with crawling.</li>
        <li>Updated the version of Bluimp image gallery.  Changed from bootstrap gallery to default lightbox one.</li>
        <li>Fixed issue where deleting a series would delete a manufacturer.</li>
        <li>Adding caching for collectible table, collectible listings, collectible photos, collectible artists, and collectible tags.</li>
        <li>Added admin page to clearing cache.</li>
        <li>Updated deploy script so that cache folders are emptied upon deploy.</li>
        <li>Updated the UI for transactions to make it cleaner.</li>
        <li>Fixed an issue where if you deleted the primary image and then the next primary image and there was still one image left, it wouldn't become the primary.</li>
    </ul>
    <h3>Release Notes for v2.14</h3>
    <ul>
        <li>Updated the user stash routes to be cleaner and more uniform. Example:
            <ul>
                <li>/user/Something/stash</li>
                <li>/user/Something/wishlist</li>
                <li>/user/Something/sale</li>
                <li>/user/Something/photos</li>
                <li>/user/Something/comments</li>
                <li>/user/Something/history</li>
            </ul>
        </li>
        <li>Added the ability to edit a listing.</li>
        <li>Added the ability to add a collectible from your wishlist, this will also remove the collectible from your wishlist.</li>
        <li>Interally cleaned up the Listing/Transaction API.</li>
    </ul>
    <h3>Release Notes for v2.13</h3>
    <ul>
        <li>Internally separated Wish List from Stash.  This was done for better future support.</li>
        <li>Updated Stash Remove, if you indicated a trade you can now enter what you traded for.  Right now this is just a text field.</li>
        <li>Updated the view for a collectible in the user's stash to show more detail.</li>
        <li>Added the ability to mark collectible for sale/trade.  This can be used to keep your Stash organized so you know what you own and what you are currently selling (and for how much).</li>
        <li>Fixed an issue where variant images were not displaying.</li>
        <li>Fixed an issue where collectible price facts were getting carried over when creating variants.</li>
        <li>Fixed an issue where unactive user collectibles were showing up in the registry.</li>
    </ul>
    <h3>Release Notes for v2.12</h3>
    <p>Added the following new sharing features:</p>
    <ul>
        <li>New menu option on the collectible detail page that allows you to share the collectible multiple ways.</li>
        <li>New multishare feature on the user Stash "Share and Edit Photo" page.  This allows you to select multiple photos at once to share.</li>
        <li>Updated the style of the menu bar on the collectible detail page.</li>
        <li>Started adding support for Open Graph.</li>
        <li>Minor bug fixes.</li>
    </ul>
    <h3>Release Notes for v2.11</h3>
    <p>Added a page for Sharing and Editing photos.  You can access this page by going to My Stash and clicking the Photos tab.  From this page you can change the title and description.  There are also some helper fields for sharing photos.</p>
    <p>Updated the landing page.</p>
    <p>UI updates.</p>
    <p>Photo resizing fixes.</p>
    <h3>Release Notes for v2.10.1</h3>
    <p>Updated the user notifications page better show what the user is being notified for.</p>
    <h3>Release Notes for v2.10</h3>
    <p>Updated the notification emails to include more information and to look sexier.</p>
    <h3>Release Notes for v2.9</h3>
    <p>Added a note field per collectible in a user's stash.  This field can be used to add notes about thier collectible.  There is also a checkbox field that will allow you to make that note private.</p>
    <h3>Release Notes for v2.8.3</h3>
    <p>Added a new sort for the user stash list page.  You can now sort by the average price of the collectible.</p>
    <p>Various UI fixes.</p>
    <h3>Release Notes for v2.8.2</h3>
    <p>Fixed an issue with navbar not collapsing when using iPad in portrait mode.</p>
    <p>Fixed an issue where the Exclusive Retailer/Venue field would allow less than 4 characters and save that value to the database.</p>
    <h3>Release Notes for v2.8.1</h3>
    <p>Bug fixes</p>
    <h3>Release Notes for v2.8</h3>
    <p>Updated to support Bootstrap v3</p>
    <p>UI fixes</p>
    <h3>Release Notes for v2.7.3</h3>
    <ul>
        <li>Updates to SEO</li>
        <li>Larger thumbnails</li>
        <li>UI updates</li>
        <li>Added condition to listing</li>
    </ul>
    <h3>Release Notes for v2.7.2</h3>
    <p>We are now automatically adding eBay items that have been relisted.</p>
    <p>Added two new fields to eBay listings, the eBay condition id (future use) and the eBay condition name.  We will display this in a future update.</p>
    <h3>Release Notes for v2.7.1.1</h3>
    <p>Removed the curreny field for now because it was interfering with the MSRP Stash value.  We will add support for multiple currencies later.</p>
    <h3>Release Notes for v2.7.1</h3>
    <p>Added a new widget to the user home page that shows the following values for your stash:</p>
    <ul>
        <li>MSPR value</li>
        <li>Total amount paid for the collectibles in your stash.</li>
        <li>Total amount of the collectibles you sold.</li>
        <li>Current Collection Stashv value based on the average price each collectible.</li>
    </ul>
    <h3>Release Notes for v2.7</h3>
    <p>Processing price data about user stashes behind the scenes.</p>
    <h3>Release Notes for v2.6.2</h3>
    <p>Updated the Account Settings to allow you to adjust how you receive notifications from Collection Stash.</p>
    <h3>Release Notes for v2.6.1</h3>
    <p>Added a subject field for all notifications so that emails can be more specific now.</p>
    <h3>Release Notes for v2.6</h3>
    <p>Added a notifications tab to the user dashboard.  This will allow you to see all notifications you received from Collection Stash.</p>
    <h3>Release Notes for v2.5.6</h3>
    <ul>
        <li>Updated the home dashboard to break the data into multiple sections.  These sections are access via the new menu on the left when you are logged in on the home page.</li>
        <li>Updated the user history list to display sold cost and date if it was added.</li>
    </ul>
    <h3>Release Notes for v2.5.5</h3>
    <p>We are now calculating average prices for collectibles with transactions.  We also made some changes to the look of the Price Guide.</p>
    <img src="/img/documentation/listing_page.png" class="img-polaroid">
    <p></p>
    <p>Read more about this brand new feature in our <a href="/pages/collection_stash_documentation#listings-price-guide">A Guide to Collection Stash</a></p>
    <h3>Release Notes for v2.5.4</h3>
    <ul>
        <li>Listings have been tabbed out on the Collectible Detail page under "Price Guide"</li>
        <li>Added a graph to show change in price over time.</li>
    </ul>
    <h3>Release Notes for v2.5.3.2</h3>
    <h4>Duplicate Part Replacement</h4>
    <p>We added the ability to replace duplicate parts.  Instead of having to remove and add the duplicates you can now directly replace it with an existing one.  Read more about this brand new features in our <a href="/pages/collection_stash_documentation#parts-duplicate">A Guide to Collection Stash</a></p>
    <h3>Release Notes for v2.5.3</h3>
    <ul>
        <li>Scales Filter</li>
        <li>Cost is no longer required when removing a stash because you sold it.</li>
        <li>More details around your stash, including worth and statistics.</li>
        <li>UI updates and bug fixes.</li>
    </ul>
    <h3>Release Notes for v2.5.2</h3>
    <h4>History</h4>
    <p>You can now maintain a history of your Stash.</p>
    <h3>Release Notes for v2.5.0.1</h3>
    <p>When you add listings to a collectible, the listings will now show up on the Activity view on your Home page.  You will also be able to earn 50 nuts for each listing you add.</p>
    <h3>Release Notes for v2.5</h3>
    <p>Version 2.5 will see major updates to the Stash.</p>
    <p>The first release for 2.5 allows you to add eBay listings to a collectible.  Using the item number found on an eBay listing, you can enter it in the "Item Number" field found when viewing a collectible.  Once you click "Add Listing", Collection Stash will retrieve the listing details and display them back to you.</p>
    <p>This will allow you to see what is currently avaliable and if collectibles have recently sold.  As of now, you will have to manually enter eBay listings as you come across them.</p>
    <p>You can read more about listings <a href="/pages/collection_stash_documentation#listings">here</a></p>
    <p>Future enhancements for 2.5 will include:</p>
    <ul>
        <li>Stash history.  You will be able to maintain a history of your evolving stash, indicating what you use to own and why it is not in your stash anymore.</li>
        <li>Filters for your stash.</li>
        <li>More details around your stash, including worth and statistics.</li>
        <li>The ability to export your stash.</li>
    </ul>
    <h3>Release Notes for v2.4</h3>
    <h4>Custom and Original Collectibles</h4>
    <p>Read more about these brand new features in our <a href="/pages/collection_stash_documentation">A Guide to Collection Stash</a></p>
    <h3>Release Notes for v2.3</h3>
    <h4>Part Photos</h4>
    <p>You can now add photos to individual parts!</p>
    <p>This works the same way that adding photos to collectibles does.  While browsing a collectible, click the "Actions" drop down and select "Edit Part Photos".</p>
    <h4>Add Artist to Part</h4>
    <p>Parts now support artists.  A part can have a manufacturer or it can have an artist.  This is useful for cases when an artist has created an unofficial custom for sale that has individual parts.  You can use this field to link them together.</p>
    <p>Right now, the artist has to be already existing to add it to the part.  Just make sure when you are adding a collectible, that the artist is attached to the collectible first.</p>
    <h4>Customs/Official Collectibles</h4>
    <p>A new field has been adding for collectibles, called "Official". This field indicates whether or not this collectible was officially licensed to make or is a custom that was made and sold.  By default all collectibles are indicated as official.</p>
    <p>With the addition of this field we are now supporting custom figures and accessories that were made in quantities and sold.  This is <strong>not</strong> for 1 of 1 pieces or personal bashes and customs, <strong>yet</strong>!  That is a future enhancement we are still working on.</p>
    <h4>Condition is not required for collectibles</h4>
    <p>The stash collectible condition field was updated so that you do not have to specify one now.</p>
    <h3>Release Notes for v2.2</h3>
    <h4>Add New Manufacturers!</h4>
    <p>You can now add new manufacturers!</p>
    <p>The major feature for release 2.2 is the ability for you to be able to add new manufacturers while you are submitting new collectibles.  When you are submitting a new collectible, you will find next to the manufacturer drop-down a button labeled "Add".</p>
    <p>This will allow you to submit the details for a new Manufacturer, including brands.  Once you added the manufacturer, you will be able to start adding collectibles for it right away.</p>
    <p>There is no approval process for adding new manufacturers, once you save it will go live!</p>
    <p>A couple things to keep in mind:</p>
    <ul>
        <li>When you add a new manufacturer, it will be linked to that collectible type you are adding.  You cannot add more collectible types right now, please let me know if you need different ones added.</li>
        <li>You can only add brands, you cannot remove them.</li>
        <li>You can also add new categories for the manufacturer (cannot remove).  Remember, these categories are <strong>specific</strong> to that manufacturer, please make sure they are appropriate for that manufacturer.</li>
        <li>If you made a mistake, don't panic! Just get a hold of me and I can help you out.</li>
    </ul>
    <h4>Update existing Manufacturers</h4>
    <p>Besides adding new manufacturers you can also update existing ones.  Right now you can add new brands and categories.  When adding a new collectible and you have a manufacturer selected, there will be buttons next to the brand and category inputs, where you can add additional ones.</p>
    <h4>Manufacturer and Artist Details</h4>
    <p>Manufacturer and artist detail pages have been added.  You can get to these pages wherever manufacturers or artists are listed.</p>
    <h4>Link to collectible from stash detail</h4>
    <p>A link has been added to the detail of the collectible in a user's stash.  Just making it easier to see the details.</p>
    <h3>Release Notes for v2.1</h3>
    <h4>Artists</h4>
    <p>Collectibles can now have artists!</p>
    <p>Artists work similar to tags and parts.  You can add as many artists as you wish to a collectible.  As of now, artists are in their simipliest form, search for an existing artist to add to the collectible or enter in a brand new one.  Please try and use their most common pen name to avoid duplicates.</p>
    <p>This is just the first step with artists.  Future enhancements will include portfilo pages, better search capabilities, and the ability to add custsoms for artists.</p>
    <h4>Prints</h4>
    <p>We have added a new collectible type!</p>
    <p>You can now add your favorite artist's prints.  Right now, the only prints you should be adding are ones that an artist has made for sale in any form.  This includes artwork an artist did for a company, a venue, or created and printed themselves.  Please note, this does not include original art as of now.</p>
    <p>Here are a couple rules to follow when adding prints:</p>
    <ul>
        <li>If the artist that created the print was commissioned by a manufacturer or producer, then make sure to select the appropriate manufacturer/producer.</li>
        <li>If the artist made the work on their own for a specific venue, then leave the manufacturer/producer field blank but add the venue.</li>
        <li>If the artist made the work on their own and printed it and sold it themselves, then leave the manufacturer/producer and the venue fields blank.</li>
    </ul>
    <p>This is just the beginning with prints, we will be adding more features around this in the future!</p>
    <h4>Photo Upload from URL</h4>
    <p>When adding photos to collectibles, you now have the option to upload from a URL (again :) ).</p>
    <h4>Signed</h4>
    <p>You can now indicate on each collectible whether it is signed as part of the sold piece.</p>
    <h3>Release Notes for v1.8</h3>
    <h4>Activites and Points</h4>
    <p>You might notice next to your username in the Community list that there is a number.  This is your total points or nuts as I like to call them.  You earn nuts by participating and using the site.  Activities that earn you nuts include adding new collectibles, updating existing collectibles, participating in discussions, inviting new users and more.</p>
    <p>For this first release we are tracking most activities that happen on the site.  We will use these activites over time to calculate your nut total as well as a way for you to see what others are up to on the site.  Right now we are not updating this score live, that will come in another release.</p>
    <p>Future announcements will be made on what nuts can be used for but until they, just stay active!</p>
    <h4>Part List</h4>
    <p>The main part list now better shows what collectibles that part is attached to.  Links are now supplied to those collectibles.</p>
    <h4>Edit Collectible - Part List</h4>
    <p>When you are adding an existing part to a collectible, it now shows you how many of that part are attached to the existing collectible.</p>
    <h4>Collectible Detail - Part List</h4>
    <p>From the collectible detail part list, you can now see what other collectibles that part is linked to.</p>
    <h3>Release Notes for v1.6</h3>
    <h4>Enhanced the photo upload process for user photos</h4>
    <p>The photo upload process has been enhanced to be more user friendly and run faster.</p>
    <h4>Gallery Feature</h4>
    <p>Using the link at the top bar, you can now look at a random sampling of our members collection photos.  Click on any image to engage the gallery feature or click their user's name to view more of their photos.</p>
    <h3>Release Notes for v1.5</h3>
    <h4>Enhanced the photo upload process for collectibles</h4>
    <p>The edit process around uploads for collectibles has been enhanced to allow for mulitple uploads per collectible.  This will allow you to add as many photos as you want to a collectible.  Adding or removing these
    photos still needs to go through the standard approval process.  Currently you cannot delete the primary image (the original one added), this will be updated in the next release.</p>
    <p>This only applies when you are editing the collectible.  When adding a new collectible the original photo upload process applies.</p>
    <h4>Stash View</h4>
    <p>The stash view has been enhanced to work with a more responsive design.  Photo sizes are not gauranteed to be the same across the board.  We are trying out a slightly new look to see how it works</p>
    <p>We also adding a list view for your stash.  There are now two icons that will allow you to toggle between different ways to view your stash, depending on what you need to do.</p>
    <h4>Collectible Parts</h4>
    <p>This is the big one!  Most of the work for this release was enhancing how we are adding and maintain collectible parts.</p>
    <p>Every collectible can be broken down into different collectible parts.  Those parts are what make up that collectible as a whole.  Some collectibles might only have one part, some might have a lot.  Those parts might also be
    shared across other collectibles.  If you have a variant of a collectible, in most cases that variant is going to be made up of parts from it's parent collectible.</p>
    <p>A collectible's parts can now be thought of as independent parts that are maintain separately and can be added or removed from collectibles.  There are a lot more actions now when you are editing or adding a collectible.</p>
    <p>When you are adding a new collectible you now have the following actions when adding parts:</p>
    <ul>
        <li>Add New Part</li>
        <li>Add Existing Part</li>
    </ul>
    <p>Adding a new part will add a brand new part with it's properties and automatically link it to the collectible you are adding.  Adding an existing part will allow you to search for a part from a different collectible to add to this collectible.</p>
    <p>When you are editing a collectible you now have the following actions when adding new parts to the collectible:</p>
    <ul>
        <li>Add New Part</li>
        <li>Add Existing Part</li>
    </ul>
    <p>When you are editing a collectible's parts you now have the following actions per collectible part:</p>
    <ul>
        <li>Edit Collectible Part</li>
        <li>Edit Part</li>
        <li>Remove Collectible Part</li>
        <li>Remove Part</li>
    </ul>
    <p>Part's now have the following properties:</p>
    <dl>
        <dt>Category</dt>
        <dd>Each part has a category.  This allows us to better organize specific collectible parts.  If you don't know what category to put the part in, please use the generic "part" category.</dd>
        <dt>Name</dt>
        <dd>This is a short description for the part.</dd>
        <dt>Description</dt>
        <dd>Use this to add any additional details for the part.</dd>
        <dt>Manufacturer</dt>
        <dd>This indicates who made the part.  A majority of the time this will be the same as the manufacturer who made the collectible.</dd>
        <dt>Scale</dt>
        <dd>Scale of the part.</dd>
        <dt>Count</dt>
        <dd>This indicates how many of the same part this collectible has.  Please note that this is not a property of the part itself but a property of the part being adding to the collectible.</dd>
    </dl>
    <p>I did this enhancement for a couple reasons.  Our collectible information will now be more accurate.  We can indicate what parts are shared across multiple collectibles.  I think this will be major for action figure collectors.  This will also allow us to
    do some bigger enhancments down the road with customs.</p>
    <h4>UI Enhancements</h4>
    Lot's of these!
    <h4>Artist Proof and Edition Number</h4>
    <p>It was pointed out to me that some collectibles can be an artist proof and a part of the collectible edition.  I have updated the process so that this is now allowed.</p>
    <h4>Remember Me!</h4>
    <p>When logging in, you can know check whether or not you want us to remember who you are for the next time you visit. </p>
</div>