<div id="attribute-upload-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">	
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Upload Photo</h4>
			</div>
			<div class="modal-body">
				<?php echo $this -> element('flash'); ?>
			
				<p><?php echo __('This will allow you to sumbit photos for the collectible you are viewing.  Each photo you submit will require approval from an admin.  All pending photos are indicated below.  If you submitted an incorrect photo and it is still pending you can delete it, otherwise you can also submit for approval the removal of a photo.  Note, you can only delete pending photos that you added.  Currently, you cannot delete the primary photo.')
				?></p>
				<p><?php echo __('Image requirements:'); ?></p>
				<ul>
					<li><?php echo __('The image must be less than 2MB.'); ?></li>
				</ul>
				<p><?php echo __('Image recommendations:'); ?></p>
				<ul>
					<li><?php echo __('The image should be at least 150 x 150 pixels.'); ?></li>
					<li><?php echo __('This will be used as the default image for this collectible.  Thumbnails will look best if this image\'s height is bigger than it\'s width.'); ?></li>
					<li><?php echo __('Please try and use a professionally shot photo.'); ?></li>
				</ul>
				
				<div class='component-message error'>
					<span></span>
				</div>
				<!-- The file upload form used as target for the file upload widget -->
				<form id="fileupload" action="server/php/" method="POST" enctype="multipart/form-data">
					<input id="upload-collectible-id" type="hidden" name="<?php echo $uploadName; ?>" value="" />
					<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
					<div class="row fileupload-buttonbar">
						<div class="col-md-7">
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-success fileinput-button"> <i class="fa fa-plus icon-white"></i> <span>Add files...</span>
								<input type="file" name="data[Upload][file]" multiple>
							</span>
						</div>
						<div class="col-md-7 url-upload">
							<span class="btn btn-success fileinput-button"> <i class="fa fa-plus icon-white"></i> <span>Upload URL</span>
								<input autocomplete="off" type="text" name="data[Upload][url]" class="url-upload-input" value="">
									<button id="upload-url" class="btn" type="button">
										Upload
									</button>
							</span>
						</div>
						<!-- The global progress information -->
						<div class="col-md-5 fileupload-progress fade">
							<!-- The global progress bar -->
							<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
								<div class="bar" style="width:0%;"></div>
							</div>
							<!-- The extended global progress information -->
							<div class="progress-extended">
								&nbsp;
							</div>
						</div>
					</div>
					<!-- The loading indicator is shown during file processing -->
					<div class="fileupload-loading"></div>
					<br>
					<!-- <div id="dropzone" class="fade well">
						Drop files here
					</div>-->
					<br>
					<!-- The table listing the files available for upload/download -->
					<div class="table-responsive">
						<table role="presentation" class="table table-striped">
							<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
						</table>						
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
