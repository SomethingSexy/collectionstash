<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">

		</div>
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
                        }?>
                </ul>
            </div>
			<div class="stash-details">
			     <h2><?php echo $stashUsername . '\'s' .__(' stash', true)?></h2>
			</div>
			<div id="collectibles">
