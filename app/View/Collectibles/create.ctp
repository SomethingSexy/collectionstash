<?php echo $this -> Html -> script('pages/page.collectible.create', array('inline' => false));?>

<div class="container">
	<div id="create-container" class="row spacer">
		<div class="col-md-12">
			<div class="jumbotron">
				<div class="container">
					<h1>Hey, Listen!</h1>
					<p>
						Are you new to Collection Stash or you want to brush up on how to add a new collectible or custom?  Read our <strong>A Guide to Collection Stash</strong>.
					</p>
					<p>
						<a href="/pages/collection_stash_documentation" class="btn btn-primary btn-large"> Learn more </a>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="thumbnail">
						<img class="mass-img" src="/img/logo/csmp.png"/>
						<div class="caption">
							<h3>Mass-produced Collectible</h3>
							<p>
								Add a standard mass-produced collectible, whether it is officially licensed or a custom made in quantities.
							</p>
							<p><a class="btn btn-primary mass" href="#">Select</a>
							<?php if($allowImport){ ?>
								<a title="Import a collectible from a url." class="btn btn-primary _import pull-right" href="javascript:void(0);">Import</a></p>
							<?php } ?>
						</div>
					</div>

				</div>
				<div class="col-md-4">
					<div class="thumbnail">
						<img src="/img/logo/csoc.png"/>
						<div class="caption">
							<h3>Original Collectible</h3>
							<p>
								Add an original collectible or commissioned piece of work that <strong>you own</strong>.  This is for collectibles where only one exist!
							</p>
							<p><a class="btn btn-primary original" href="#">Select</a></p>
						</div>
					</div>

				</div>
				<div class="col-md-4">
					<div class="thumbnail">
						<img src="/img/logo/cscustoms.png"/>
						<div class="caption">
							<h3>Custom Collectible</h3>
							<p>
								Customs are collectibles that you built or bashed together <strong>yourself</strong>.  This includes customs where you made everything or that you pieced together from existing parts.
							</p>
							<p><a class="btn btn-primary custom" href="#">Select</a></p>
						</div>
					</div>

				</div>
			</div>			
			
		</div>
	</div>	
</div>
