
<?php echo $this -> element('admin_actions');?>
<div class="col-md-8">
    <div class="page">
        <div class="title">
            <h2><?php echo __('Edit Manufacturer');?></h2>
        </div>
        <?php echo $this -> element('flash');?>
        <div class="licenses view">
            <?php echo $this -> Form -> create('Manufacture', array('class'=>"form-horizontal"));?>
	            <fieldset>
					<div class="form-group">
						<label class="col-lg-3 control-label" for="inputCode">Name</label>
						<div class="col-lg-6">
							<?php echo $this -> Form -> input('title', array('label' => false, 'div' => false, 'class'=>"form-control")); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label" for="inputCode">URL</label>
						<div class="col-lg-6">
							<?php echo $this -> Form -> input('url', array('label' => false, 'div' => false, 'class'=>"form-control")); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label" for="inputCode">Series ID</label>
						<div class="col-lg-6">
							<?php echo $this -> Form -> input('series_id', array('type' => 'text', 'label' => false, 'div' => false, 'class'=>"form-control")); ?>
						</div>
					</div>
	                <?php echo $this -> Form -> hidden('id')?>
					<div class="form-group">
						<div class="col-lg-offset-3 col-lg-9">
						<button type="submit" class="btn btn-primary save" data-loading-text="Loading...">
							Save
						</button>
						</div>
					</div>
	            </fieldset>
      		</form>
        </div>
    </div>
</div>
