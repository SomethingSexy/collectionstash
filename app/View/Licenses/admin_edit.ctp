<?php echo $this -> element('admin_actions'); ?>
<div class="col-md-10">
    <div class="title">
        <h2><?php echo __('Edit Brand'); ?></h2>
    </div>
    <?php echo $this -> element('flash'); ?>
    <div class="licenses view">
        <?php echo $this -> Form -> create('License', array('class'=>"form-horizontal")); ?>
        <fieldset>
			<div class="form-group">
				<label class="col-lg-3 control-label" for="inputCode">Name</label>
				<div class="col-lg-6">

					<?php echo $this -> Form -> input('name', array('label' => false, 'div' => false, 'class' => "form-control")); ?>
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
