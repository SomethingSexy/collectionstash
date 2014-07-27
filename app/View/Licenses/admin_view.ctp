<?php echo $this -> element('admin_actions');?>
<div class="col-md-10">
	<div class="title">
		<h2><?php echo __('Brand Detail');?></h2>
		<div class="actions icon">
			<ul>
				<li>
					<a id="remove-link" class="link" data-id="<?php echo $license['License']['id'];?>"> <img src="/img/icon/trash_2.png"> </a>
					<form id="remove-form" action="/admin/licenses/remove/<?php echo $license['License']['id'];?>" method="post"></form>
				</li>
				<li>
					<a href="/admin/licenses/edit/<?php echo $license['License']['id'];?>"> <i class="fa fa-pencil fa-lg"></i> </a>
				</li>
			</ul>
		</div>
	</div>
	<?php echo $this -> element('flash');?>
	<div class="license view">
		<div class="license detail">
			<dl>
				<dt>
					<?php echo __('Name');?>
				</dt>
				<dd>
					<?php echo $license['License']['name'];?>
				</dd>
				<dt>
					<?php echo __('Collectible Count');?>
				</dt>
				<dd>
					<?php echo $license['License']['collectible_count'];?>
				</dd>
			</dl>
		</div>
		<?php
        if (!empty($license['LicensesManufacture'])) {
            echo '<div class="standard-list">';
            echo '<ul>';
            foreach ($license['LicensesManufacture'] as $key => $manufacturer) {
                echo '<li>';
                echo '<span class="name">';
                echo $manufacturer['Manufacture']['title'];
                echo '</span>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="standard-list empty">';
            echo '<ul>';
            echo '<li>No manufacturers have this brand.</li>';
            echo '</ul>';
            echo '</div>';
        }
		?>
	</div>
</div>
<script>
	$(function() {
		$("#remove-dialog").dialog({
			'autoOpen' : false,
			'width' : 500,
			'height' : 'auto',
			'resizable' : false,
			'modal' : true,
			'buttons' : {
				"Remove" : function() {
					$('#remove-form').submit();
				}
			}
		});
		$('#remove-link').click(function() {
			$('#remove-dialog').dialog('open');
		});
	});

</script>
<div id="remove-dialog" class="dialog" title="Remove Brand">
	<div class="component component-dialog">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Are you sure you want to remove this brand?')
					?>
				</div>
			</div>
		</div>
	</div>
</div>