<?php echo $this->element('stash_top'); ?>
	<?php
	if (isset($collectibles) && !empty($collectibles)) {
		echo '<div id="tiles" data-username="' . $stashUsername . '">';
	
		echo '<div class="glimpse" data-current="' . $this -> Paginator -> current() . '"';
		if ($this -> Paginator -> hasPrev()) {
			echo 'data-hasprev="true"';
		} else {
			echo 'data-hasprev="false"';
		}
		if ($this -> Paginator -> hasNext()) {
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
				echo '<a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . '&nbsp;' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
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
		if ($this -> Paginator -> hasPrev()) {
			echo '<div id="previous" class="tn3e-prev" title="Previous Collectibles"></div>';
		}
		if ($this -> Paginator -> hasNext()) {
			echo '<div id="next" class="tn3e-next" title="Next Collectibles"></div>';
		}
		echo '</div>';
	
		echo '</div>';
	} else {
		echo '<p class="">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
	}
	?>
<?php echo $this->element('stash_bottom'); ?>			
<script>
	$(function() {
		var photosLoaded = false;
		var collectiblesLoaded = false;
		if(window.location.hash === '#collectibles' || window.location.hash === "") {
			collectiblesLoaded = true;

		} else if(window.location.hash === '#photos') {
			if(0 < photoData.length) {
				$("#photo-gallery").galleria({
					width : 900,
					height : 600,
					lightbox : true,
					data_source : photoData,
					_showDetailInfo : false
				});
			} else {
				$("#photo-gallery").parent().prepend($('<p></p>').text('No photos have been added!'));
			}
			photosLoaded = true;
		}

		$("#tabs").tabs({
			select : function(event, ui) {
				if(ui.tab.hash === '#collectibles' && !collectiblesLoaded) {
					collectiblesLoaded = true;

				} else if(ui.tab.hash === '#photos' && !photosLoaded) {
					if(0 < photoData.length) {
						$("#photo-gallery").galleria({
							width : 900,
							height : 600,
							lightbox : true,
							data_source : photoData,
							_showDetailInfo : false
						});
					} else {
						$("#photo-gallery").parent().prepend($('<p></p>').text('No photos have been added!'));
					}
					photosLoaded = true;
				}
			},
			show : function(event, ui) {
				window.location.hash = ui.tab.hash;
			}
		});
	});
	$(function() {
		var isHandlerActive = true;
		$(document).on('click', '#tiles .links div', function(event) {
			if(!isHandlerActive) {
				return;
			}
			isHandlerActive = false;
			var current = $('#tiles').children('div.glimpse').attr('data-current');
			var slide = 'right';
			if($(this).attr('id') === 'next') {
				current = parseInt(current) + 1;
			} else if($(this).attr('id') === 'previous') {
				current = parseInt(current) - 1;
				slide = 'left';
			}
			$.get('/stashs/pageView/' + $('#tiles').attr('data-username') + '/page:' + current, function(data) {
				$('#tiles').children().remove();
				$('#tiles').append(data);
				var effect = function() {
					return $('#tiles').children('.glimpse').show('slide', {
						direction : slide
					}, 1000);
				};

				$.when(effect()).done(function() {
					isHandlerActive = true;
				});
			});
			return false;
		});
	});
</script>
