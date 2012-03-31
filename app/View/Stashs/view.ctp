<div id="my-stashes-component" class="component">
    <div class="inside">
        <div class="component-title"></div>
        <?php echo $this -> element('flash');?>
        <div class="component-view">
            <div class="actions icon">
                <ul>
                    <?php
                    if (isset($myStash) && $myStash) {
                        echo '<li><a title="Add Collectibles" class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link_25x25.png"/></a></li>';
                        echo '<li>';
                        echo '<a title="Edit" class="link glimpse-link" href="/stashs/edit/' . $stashUsername . '"><img src="/img/icon/pencil.png"/></a>';
                        echo '</li>';
                    }
                    ?>
                    <li>
                        <?php echo '<a title="Photo Gallery" class="link detail-link" href="/stashs/view/' . $stashUsername . '"><img src="/img/icon/photos.png"/></a>';?>
                    </li>
                    <?php
                    if (isset($myStash) && $myStash) {
                        if (Configure::read('Settings.User.uploads.allowed')) {
                            echo '<li><a title="Upload Photos" class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_photo.png"/></a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="stash-details">
                <h2><?php echo $stashUsername . '\'s' .__(' stash', true)
                ?></h2>
                <dl>
                    <dt>Total Collectibles: </dt>
                    <dd>45</dd>
                </dl>
            </div>
            <div id="collectibles" class="collectibles">
                <div id="collectibles-gallery"></div>
            </div>
            <div id="photos" class="photos">
                <div id="photo-gallery"></div>
            </div>
            <div id="comments" class="comments-container" data-type="stash" data-typeID="<?php echo $stash['id']; ?>">
                <!-- This is where all the comments will go-
                <ol class="comments">
                    <li class="comment">
                        <div class="info">
                            <span class="user"></span>
                            <span class="datetime"></span>
                        </div>
                        <!-- This is the actual comment 
                        <div class="text"></div>
                        <div class="actions">
                            reply
                            delete
                            edit
                        </div>
                    </li>
                </ol>
                <div class="actions">
                    
                </div>
                <div class="post-comment-container">
                    <form id="CommentViewForm" accept-charset="utf-8" method="post" action="/comments/add">
                        <div style="display:none;">
                            <input type="hidden" value="POST" name="_method">
                        </div>
                        <div class="input textarea required">
                            <div class="label-wrapper">
                                <label for="CommentComment">Comment</label>
                            </div>
                            <textarea id="CommentComment" rows="6" cols="30" name="data[Comment][comment]"></textarea>
                        </div>
                    </form>                    
                </div>-->
            </div>
        </div>
    </div>
</div>
<?php echo $this -> Html -> script('galleria-1.2.6', array('inline' => false));?>
<?php echo $this -> Html -> script('galleria.classic.js', array('inline' => false));?>
<?php echo $this -> Html -> script('jquery.comments', array('inline' => false));?>
<?php echo $this -> Html -> css('galleria.classic');?>

<script>
    var photoData = [<?php
if (isset($userUploads) && !empty($userUploads)) {

    foreach ($userUploads as $key => $userUpload) {
        echo '{';
        echo 'image : "' . $this -> FileUpload -> image($userUpload['name'], array('width' => 0, 'imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
        echo 'thumb : "' . $this -> FileUpload -> image($userUpload['name'], array('imagePathOnly' => true, 'height' => 100, 'width' => 100, 'title' => $userUpload['title'], 'alt' => $userUpload['description'], 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
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
    
</script>

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
	    
	    $('#comments').comments();
	    
		var photosLoaded = false;
		var collectiblesLoaded = false;
		// if(window.location.hash === '#collectibles' || window.location.hash === "") {
			if(0 < collectibleData.length) {
				$("#collectibles-gallery").galleria({
					width : 600,
					height : 400,
					lightbox : true,
					data_source : collectibleData,
					_showDetailInfo : true,
					debug: false
				});
			} else {
				$("#collectibles-gallery").parent().prepend($('<p></p>').text('No collectibles have been added!'));
			}
			collectiblesLoaded = true;

		// } else if(window.location.hash === '#photos') {
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
		// }

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
