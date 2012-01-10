<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
					<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
						<a href="#tabs-1"><?php echo __('Collectibles');?></a>
					</li>
					<li class="ui-state-default ui-corner-top">
						<a href="#tabs-2"><?php echo __('Photos');?></a>
					</li>
					<li class="ui-state-default ui-corner-top">
						<a href="#tabs-3"><?php echo __('Discussion');?></a>
					</li>
				</ul>
				<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="actions">
						<ul>
							<?php
							if (isset($myStash) && $myStash) {
								echo '<li><a class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link.png"/></a></li>';
							}
							?>
							<li>
								<?php //echo '<a class="link detail-link" href="/stashs/view/' . $stashUsername . '/view:detail"><img src="/img/icon/detail_link.png"/></a>';?>
							</li>
							<li>
								<?php //echo '<a class="link glimpse-link" href="/stashs/view/' . $stashUsername . '/view:glimpse"><img src="/img/icon/glimpse_link.png"/></a>';?>
							</li>
						</ul>
					</div>
					<?php
					if (isset($collectibles) && !empty($collectibles)) {
						echo '<div id="tiles" data-username="'. $stashUsername.'">';
						
						echo '<div class="glimpse" data-current="' . $this -> Paginator -> current(). '"';
						if($this -> Paginator -> hasPrev()){
							echo 'data-hasprev="true"';
						} else {
							echo 'data-hasprev="false"';
						}
						if($this -> Paginator -> hasNext()){
							echo 'data-hasnext="true"';
						} else {
							echo 'data-hasnext="false"';
						}						
						
						
						echo '>';
						$count = 0;
						$collectiblesCount = count($collectibles) - 1;
						foreach ($collectibles as $key => $myCollectible) {
							$newline = false;
							$endline = false;
							//First chec to see if we are starting over
							if ($count === 0) {
								$newline = true;
								$count += $count + 1;
							} else {
								//If not first, check to see if we are on the 5th one or not
								if ($count % 4 != 0) {
									//if we are not, lets increase
									$count += $count + 1;
								} else {
									//if we are, then lets start over and make sure we close out the div.
									$count = 0;
									$endline = true;
								}
							}

							if (!$endline) {
								if (($collectiblesCount) === $key) {
									$endline = true;
								}
							}

							if ($newline) {
								echo '<div class="line">';
							}
							if (!empty($myCollectible['Collectible']['Upload'])) {
								echo '<div class="image">';
								echo '<a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
								echo '</div>';
								//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
							} else {
								echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
							}
							if ($endline) {
								echo '</div>';
							}
						}
						echo '</div>';
						
						echo '<div class="links">';
						if($this -> Paginator -> hasPrev()){
							echo '<div id="previous" class="tn3e-prev" title="Previous Collectibles"></div>';
						} 
						if($this -> Paginator -> hasNext()){
							echo '<div id="next" class="tn3e-next" title="Next Collectibles"></div>';
						}
						echo '</div>';
						
						echo '</div>';
					} else {
						echo '<p class="">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
					}
					?>
				</div>
				<div id="tabs-2" class="ui-tabs-hide">
					<div class="actions">
						<ul>
							<?php
							if (isset($myStash) && $myStash) {
								if (Configure::read('Settings.User.uploads.allowed')) {
									echo '<li><a class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_link.png"/></a></li>';
								}
							}
							?>
						</ul>
					</div>
					<div id="gallery"></div>
				</div>
				<div id="tabs-3" class="ui-tabs-hide"></div>
			</div>
		</div>
	</div>
</div>
<?php echo $this -> Html -> script('galleria-1.2.5', array('inline' => false));?>
<?php echo $this -> Html -> script('galleria.classic.js', array('inline' => false));?>
<?php echo $this -> Html -> css('galleria.classic');?>

<script>
	$(function() {
		$("#tabs").tabs();
	});
	
	$(function(){
		var isHandlerActive = true;
		$(document).on('click','#tiles .links div', function(event){
			if(!isHandlerActive){
				return; 
			}
			isHandlerActive = false;
			var current = $('#tiles').children('div.glimpse').attr('data-current');
			var slide = 'right';
			if($(this).attr('id') === 'next'){
				current = parseInt(current) + 1;	
			} else if($(this).attr('id') === 'previous'){
				current = parseInt(current) - 1;	
				slide = 'left';
			} 
			$.get('/stashs/pageView/' + $('#tiles').attr('data-username') +'/page:' + current, function(data) {
			  	$('#tiles').children().remove();
			  	$('#tiles').append(data);
			  	var effect = function() {
					return $('#tiles').children('.glimpse').show('slide', {direction: slide}, 1000);
				};

				$.when( effect() ).done(function() {
					 isHandlerActive = true;
				});
			});
			return false;
		});
		
	});
	
	$(function() {

var data = [<?php
if (isset($userUploads) && !empty($userUploads)) {

	foreach ($userUploads as $key => $userUpload) {
		echo '{';
		echo 'image : "' . $this -> FileUpload -> image($userUpload['name'], array('width' => 0, 'imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'thumb : "' . $this -> FileUpload -> image($userUpload['name'], array('imagePathOnly' => true, 'height' => 40, 'width' => 41, 'title' => $userUpload['title'], 'alt' => $userUpload['description'], 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'title : "' . $userUpload['title'] . '",';
		echo 'description : "' . $userUpload['description'] . '"';
		echo '}';
		if ($key != (count($userUploads) - 1)) {
			echo ',';
		}
	}
}
?>
	];
	if(0 < data.length) {
		$("#gallery").galleria({
			width : 900,
			height : 500,
			lightbox : true,
			data_source : data
		});
	} else {
		$('#gallery').parent().prepend($('<p></p>').text('No photos have been added!'));
	}
	});
</script>

