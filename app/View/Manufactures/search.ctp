<?php
echo $this -> Html -> scriptBlock('var uploadDirectory ="' . $this -> FileUpload -> getUploadDirectory() .'";', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawCompanies =' . json_encode($companies) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawPermissions =' . json_encode($permissions) .';', array('inline' => false));

if(isset($brands)){
	echo $this -> Html -> scriptBlock('var rawBrands =' . json_encode($brands) .';', array('inline' => false));	
}

echo $this -> Html -> script('pages/page.company.list', array('inline' => false));?>


<h3><?php echo __('Company/Manufacturer List'); ?></h3>
	<?php
	if (!isset($isLoggedIn) || !$isLoggedIn) {
	?>
	<div class="row">
		<div class="col-md-7">
			<div class="alert alert-info">
  				<strong>Hey! Listen!</strong>
  				Not finding what you are looking for? Are we missing a collectible?  <a href="/users/login">Log in</a> or <a href="/users/register">register</a> to help us maintain an accurate and up-to-date collectible database.
    		</div>			
		</div>
	</div>
	<?php } ?>
	<div class="row" id="companies-layout">
		<div class="col-md-9">
				<div class="row spacer">
					<div class="col-md-12 _search">			

					</div>
				</div>
				
				<div class="row" data-toggle="modal-gallery" data-target="#modal-gallery">
					<div class="col-md-12 _list">			

					</div>
				</div>	
				<div class="row">
					<div class="col-md-12 _pagination">	
					
					</div>
				</div>			
		</div>
	</div>

