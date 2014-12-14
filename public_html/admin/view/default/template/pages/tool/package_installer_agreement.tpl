<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
	<div class="panel-heading">
		<div class="primary_content_actions pull-left"></div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']. (is_array($form['hidden']) ? implode('',$form['hidden']) : $form['hidden']); ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo $heading_title; ?></label>
		<div id="license" class="pre-scrollable">
			<div class="col-sm-7 col-sm-offset-3"><?php echo $agreement_text; ?></div>
		</div>
	</div>

	<div class="panel-footer">
			<div class="text-center">
				<button id="agree_btn" class="btn btn-primary">
					<i class="fa fa-check"></i> <?php echo $form['submit']->text; ?>
				</button>
				<?php echo $form['agree']; ?>
				&nbsp;
				<?php if($form['disagree_button']){?>
				<a class="btn btn-default" href="<?php echo $form['disagree_button']->href; ?>"><?php echo $form['disagree_button']->text; ?></a>
				<?php } ?>
			</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->

<script type="text/javascript">
	$('#agree_btn').click(function(){
		$('#Frm_<?php echo $form['agree']->name?>').val(1);
	});
</script>