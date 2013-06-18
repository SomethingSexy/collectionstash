<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot.categories', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot.stack', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>


<div id="my-stashes-component" class="span12">

		<div class="page-header">
			<h1><?php echo __('History'); ?></h1>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="row-fluid">
			<div class="span8">
				<div class="actions stash">
					<ul class="nav nav-pills">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li class="selected"><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
				</div>
				
			</div>
			<div class="span4">
				<!--
				<div class="btn-group pull-right years">
				    <button class="btn">All</button>
				    <button class="btn">Sold</button>
			    </div>			-->	
			</div>
			
		</div>
			
		

			
	<div class="row">
		<div class="span6">
			<div class="">
				<h2>Bought and Sold</h2>
				<div class="btn-group years">
				<?php
				$default = key($graphData);
				foreach ($graphData as $keyYear => $year) {
					echo '<a data-key="' . $keyYear . '" class="btn';
					if ($keyYear === $default) {
						echo ' active';
					}
					echo '">' . $keyYear . '</a>';
				}
				?> 
			    </div>	
				<div id="holder" style="width:600px;height:300px">
					<?php
					if (empty($graphData)) {
						echo '<p>' . __(' Not enough information to draw Bought and Sold graph.', true) . '</p>';
					}
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span6 well">
			<div class="">
					<?php
					if (isset($collectibles) && !empty($collectibles)) {
						echo $this -> element('stash_table_list', array('collectibles' => $collectibles, 'showThumbnail' => false, 'stashType' => 'default', 'history' => true));
					} else {
						echo '<p>' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
					}
	?>
			</div>
		</div>
	</div>
</div>

<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?><?php
	$counts = '';

	foreach ($graphData as $keyYear => $year) {
		$counts .= $keyYear . ':[[';
		foreach ($year as $keyMonth => $month) {
			$counts .= '[' . $keyMonth . ' ,' . count($month['purchased']) . '],';
		}
		$counts .= '],[';
		foreach ($year as $keyMonth => $month) {
			$counts .= '[' . $keyMonth . ' ,' . count($month['sold']) . '],';
		}
		$counts .= ']],';
	}
	echo 'var data = {' . $counts . '};';
	echo 'var bdata = JSON.parse(JSON.stringify(data["' . $default . '"]));';
?>
	$(function() {
		$.plot("#holder", bdata, {
			series : {
				stack : false,
				bars : {
					show : true,
					barWidth : 0.6,
					align : "center"
				}
			},
			xaxis : {
				mode : "categories",
				tickLength : 0
			},
			yaxis : {
				minTickSize : 1
			}
		});

		$('.years').on('click', '.btn', function(event) {
			$(event.currentTarget).siblings().removeClass('active');
			$(event.currentTarget).addClass('active');
			bdata = JSON.parse(JSON.stringify(data[$(event.currentTarget).attr('data-key')]));
			$.plot("#holder", bdata, {
				series : {
					stack : false,
					bars : {
						show : true,
						barWidth : 0.6,
						align : "center"
					}
				},
				xaxis : {
					mode : "categories",
					tickLength : 0
				},
				yaxis : {
					minTickSize : 1
				}
			});
		});
	});
</script>