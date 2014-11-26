<div id="collectible-container" class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if (!empty($attribute['AttributesUpload'])) {
                                	foreach ($attribute['AttributesUpload'] as $key => $upload) {
                                		if ($upload['primary']) {
                                			$this -> set('og_image_url', 'http://' . env('SERVER_NAME') . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)));
                                			echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $attribute['Attribute']['description'], 'imagePathOnly' => false)) . '</a>';
                                			break;
                                		}
                                	}
                                } else {
                                	echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        if (!empty($attribute['AttributesUpload']) && count($attribute['AttributesUpload']) > 1) {
                        ?>
                        <div class="row spacer">
                            <div class="col-md-12">
                                <div id="carousel-example-generic" class="carousel slide">
                                    <!-- Indicators
                                    <ol class="carousel-indicators">
                                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                                    </ol> -->
                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner">
                                        <?php
                                        $i = 1;
                                        foreach ($attribute['AttributesUpload'] as $key => $upload) {
	                                        if (!$upload['primary']) {
		                                        if ($i % 3 == 1) {
		                                        	echo '<div class="item ';
		                                            if ($i === 1) {
		                                            	echo 'active';
		                                            }
		                                            	echo '"><div class="row">';
		                                        }
		                                        echo '<div class="col-sm-4"><a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $attribute['Attribute']['description'], 'imagePathOnly' => false)) . '</a></div>';
		                                        if ($i % 3 == 0) {
		                                            echo '</div></div>';
		                                        }
		                                        $i++;
	                                        }
                                        }
                                        if ($i % 3 != 1) {
                                        	echo '</div></div>';
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    if (count($attribute['AttributesUpload']) > 4) {
                                    ?>
                                    <!-- Controls -->
                                    <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                                    <a class="right carousel-control" href="#carousel-example-generic" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-header">
                <h1 class="title">
                <?php
				if (!empty($attribute['Attribute']['name'])) {
				echo __('Part') . ':' . $attribute['Attribute']['name'];
				} else {
				echo __('Part');
				}
                ?>
                </h1>
            </div>
            <div class="row">
                <div class="col-md-12">
                <dl>
                    <dt>
                    <?php echo __('Added By'); ?>
                    </dt>
                    <dd>
                    <?php
                    if (!empty($attribute['User']['username'])) {
                    echo $attribute['User']['username'];
                    } else {
                    echo '&nbsp;';
                    }
                    ?>
                    </dd>
                    <dt>
                    <?php echo __('Date Added'); ?>
                    </dt>
                    <dd>
                    <?php
					$datetime = strtotime($attribute['Attribute']['created']);
					$mysqldate = date("m/d/y g:i A", $datetime);
					echo $mysqldate;
					?>
                    </dd>
                    <?php
					echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'attribute_category_id'), __('Category', true), array('value' =>  $attribute['AttributeCategory']['path_name'], 'compare' => false));
					echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'name'), __('Name', true), array('compare' => false));
					echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'description'), __('Description', true), array('compare' => false));
					echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Manufacture', 'Field' => 'title'), __('Manufacturer', true), array('compare' => false));
					echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => false));
					?>
                </dl>                
                </div>
            </div>
        </div>
    </div>
</div>