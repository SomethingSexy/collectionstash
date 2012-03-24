<?php echo $this -> element('stash_top');?>
<div id="collectibles-gallery"></div>
<?php echo $this -> element('stash_bottom');?>

<script>

	var collectibleData = [<?php
if (isset($collectibles) && !empty($collectibles)) {
	foreach ($collectibles as $key => $myCollectible) {

		//build collectible detail
		$detail = '';

		$detail .= '<div class=\"collectible detail\">';
		$detail .= '<dl>';
		$detail .= '<dt>';
		$detail .= __('Date Added');
		$detail .= '</dt>';
		$detail .= '<dd>';

		$datetime = strtotime($myCollectible['CollectiblesUser']['created']);
		$mysqldate = date("m/d/y g:i A", $datetime);
		$detail .= $mysqldate;
		$detail .= '</dd>';

		$editionSize = $myCollectible['Collectible']['edition_size'];
		if ($myCollectible['Collectible']['showUserEditionSize'] && isset($myCollectible['CollectiblesUser']['edition_size']) && !empty($myCollectible['CollectiblesUser']['edition_size'])) {
			$detail .= '<dt>';
			$detail .= __('Edition Size');
			$detail .= '</dt>';
			$detail .= '<dd>';
			$detail .= $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'];
			$detail .= '</dd>';
		}

		if (isset($myCollectible['CollectiblesUser']['artist_proof'])) {
			$detail .= '<dt>';
			$detail .= __('Artist\'s Proof');
			$detail .= '</dt>';
			$detail .= '<dd>';
			if ($myCollectible['CollectiblesUser']['artist_proof']) {
				$detail .= __('Yes');
			} else {
				$detail .= __('No');
			}
			$detail .= '</dd>';
		}

		$detail .= '<dt>';
		$detail .= __('Purchase Price');
		$detail .= '</dt>';
		$detail .= '<dd>';
		$detail .= '$' . $myCollectible['CollectiblesUser']['cost'];
		$detail .= '</dd>';

		if (isset($myCollectible['Condition']) && !empty($myCollectible['Condition'])) {
			$detail .= '<dt>';
			$detail .= __('Condition');
			$detail .= '</dt>';
			$detail .= '<dd>';
			$detail .= $myCollectible['Condition']['name'];
			$detail .= '</dd>';
		}

		if (isset($myCollectible['Merchant']) && !empty($myCollectible['Merchant'])) {
			$detail .= '<dt>';
			$detail .= __('Purchased From');
			$detail .= '</dt>';
			$detail .= '<dd>';
			$detail .= $myCollectible['Merchant']['name'];
			$detail .= '</dd>';
		}

		if (isset($myCollectible['CollectiblesUser']['purchase_date']) && !empty($myCollectible['CollectiblesUser']['purchase_date'])) {
			$detail .= '<dt>';
			$detail .= __('Date Purchased');
			$detail .= '</dt>';
			$detail .= '<dd>';
			$detail .= $myCollectible['CollectiblesUser']['purchase_date'];
			$detail .= '</dd>';
		}
		$detail .= '<dt><a href=\"/collectibles/view/' . $myCollectible['Collectible']['id'] . ' \" class=\"link\">Collectible Details</a></dt>';
		$detail .= '</dl>';

		$detail .= '</div>';

		echo '{';
		echo 'image : "' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => 0, 'imagePathOnly' => true, 'uploadDir' => 'files')) . '",';
		echo 'thumb : "' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('imagePathOnly' => true, 'height' => 100, 'width' => 101, 'uploadDir' => 'files')) . '",';
		echo 'title : "' . $myCollectible['Collectible']['name'] . '",';
		echo 'description : "' . $myCollectible['Collectible']['Collectibletype']['name'] . ' by ' . $myCollectible['Collectible']['Manufacture']['title'] . '",';
		echo 'detailDescription : " ' . $detail . '"';
		echo '}';
		if ($key != (count($collectibles) - 1)) {
			echo ',';
		}

	}

}
?>
	];

	$(function() {
		var photosLoaded = false;
		var collectiblesLoaded = false;
		if(window.location.hash === '#collectibles' || window.location.hash === "") {
			if(0 < collectibleData.length) {
				$("#collectibles-gallery").galleria({
					width : 900,
					height : 600,
					lightbox : true,
					data_source : collectibleData,
					_showDetailInfo : true,
					debug: false
				});
			} else {
				$("#collectibles-gallery").parent().prepend($('<p></p>').text('No collectibles have been added!'));
			}
			collectiblesLoaded = true;

		} else if(window.location.hash === '#photos') {
			if(0 < photoData.length) {
				$("#photo-gallery").galleria({
					width : 900,
					height : 600,
					lightbox : true,
					data_source : photoData,
					_showDetailInfo : false,
					debug: false
				});
			} else {
				$("#photo-gallery").parent().prepend($('<p></p>').text('No photos have been added!'));
			}
			photosLoaded = true;
		}

		$("#tabs").tabs({
			select : function(event, ui) {
				if(ui.tab.hash === '#collectibles' && !collectiblesLoaded) {
					if(0 < collectibleData.length) {
						$("#collectibles-gallery").galleria({
							width : 900,
							height : 600,
							lightbox : true,
							data_source : collectibleData,
							_showDetailInfo : true,
							debug: false
						});
					} else {
						$("#collectibles-gallery").parent().prepend($('<p></p>').text('No collectibles have been added!'));
					}
					collectiblesLoaded = true;

				} else if(ui.tab.hash === '#photos' && !photosLoaded) {
					if(0 < photoData.length) {
						$("#photo-gallery").galleria({
							width : 900,
							height : 600,
							lightbox : true,
							data_source : photoData,
							_showDetailInfo : false,
							debug: false
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
	$(function() {

	});

</script>
