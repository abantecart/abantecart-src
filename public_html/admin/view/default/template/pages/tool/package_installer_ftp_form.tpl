<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>


<div class="warning alert alert-info">
	<i class="fa fa-info fa-fw"></i> <?php echo $warning_ftp; ?>
	<?php if($warning_ftp_details){?>
		<pre class="pre-scrollable mt10"><?php echo $warning_ftp_details; ?></pre>
		
		<div class="text-center">
			<button class="btn btn-primary install_retry lock-on-click">
				<i class="fa fa-refresh"></i> <?php echo $button_retry; ?>
			</button>
		</div>
				
	<?php } ?>
</div>

<div class="tab-content">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left"></div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">

		<label class="h4 heading"><?php echo $heading_title; ?></label>
		<?php
		$fields = array('fhost','fuser','fpass');
		foreach ($fields as $name) { ?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $form[$name]->element_id; ?>"><?php echo $$name; ?></label>
				<div class="input-group afield col-sm-7 col-xs-12">
					<?php echo $form[$name]; ?>
				</div>
				<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
		<?php }  ?><!-- <div class="fieldset"> -->

	</div>
	<div class="panel-footer">
			<div class="text-center">
				<button class="btn btn-primary">
					<i class="fa fa-cogs"></i> <?php echo $form['submit']->text; ?>
				</button>
			</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->
<script type="application/javascript">

$(document).ready(function(){    
	$('.install_retry').on('click',function(){
		location.reload();
	});
});


</script>