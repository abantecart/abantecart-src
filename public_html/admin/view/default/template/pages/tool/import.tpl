<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ( isset($results) ) { ?>

	<?php if ( isset($results['sql']) ): ?>
		<div class="success alert alert-success">
			<?php echo $text_test_completed . $count_test_sqls; ?>.&nbsp;
			<a id="show_results" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
		</div>
		<div id="test_results" style="margin:20px; width: 800px; display: none;">
			<?php foreach($results['sql'] as $msg): ?>
				<p><?php echo $msg; ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( !empty($results) ) { ?>
		<div class="success alert alert-success"><?php echo $text_loaded . $count_loaded . '. ' . $text_updated . $count_updated . '. ' . $text_created . $count_created . '. ' . $text_errors . $count_errors; ?></div>
	<?php } ?>
	<?php if ( is_array($results['error']) ): ?>
		<div class="warning alert alert-error alert-danger">
			<?php echo $text_some_errors; ?> <a id="show_errors" href="javascript:void(0);"><b><?php echo $text_show_details; ?></b></a>
		</div>
		<div id="error_results" style="margin:20px; width: 800px; display: none;">
			<?php foreach ($results['error'] as $val) { ?>
				<div class="warning alert alert-error alert-danger"><?php echo $val; ?></div>
			<?php } ?>
		</div>
	<?php endif; ?>
<?php } ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<?php foreach($tabs as $tab){?>
		<li <?php echo ( $active == $tab ? 'class="active"' : '' ) ?>>
			<a href="<?php echo ${'link_'.$tab}; ?>"><span><?php echo ${'tab_'.$tab}; ?></span></a></li>
	<?php } ?>
	<?php echo $this->getHookVar('import_export_tabs'); ?>
</ul>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $file_form_open; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $text_load_file; ?></label>

		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12"><?php echo $text_file_field; ?></label>
			<div class="input-group afield col-sm-6">
				<?php echo $file_field; ?> 
			</div>
		</div>

		<?php foreach ($options['item'] as $optionName => $option) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12"><?php echo $options['text'][$optionName]; ?></label>
			<div class="input-group afield">
				<?php echo $option; ?>
			</div>
		</div>		
		<?php } ?>
		
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
		</div>
	</div>
	</form>

</div>


<script type="text/javascript">

	$('#show_results').click(function()
	{
		$('#test_results').slideToggle();
	})

	$('#show_errors').click(function()
	{
		$('#error_results').slideToggle();
	});

	$(document).ready(function()
	{
		$('.aform').show();
	});

</script>