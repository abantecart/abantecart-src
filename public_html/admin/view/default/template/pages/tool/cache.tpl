<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
	<div class="panel-heading">
		<div class="pull-left">
			<button class="btn btn-primary" id="clearall" type="button"><i class="fa fa-trash-o"></i> <?php echo $text_clear_all_cache;?></button>
		</div>
		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<?php if (!empty ($help_url)) : ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>

		<table class="table table-striped">
			<thead>
			<tr>
				<th class="center"><input id="checkall" type='checkbox'/></th>
				<th><?php echo $column_type; ?></th>
				<th class="left"><?php echo $column_description; ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ($sections) { ?>
				<?php foreach ($sections as $k=>$section) { ?>
					<tr>
						<td class="center"><input id="chk_<?php echo $k;?>" type='checkbox' name="selected[]" value="<?php echo $section['keywords']; ?>"/></td>
						<td class="left"><label for="chk_<?php echo $k;?>"><?php echo $section['text']; ?></label></td>
						<td class="left"><label for="chk_<?php echo $k;?>"><?php echo $section['description']; ?></label></td>
					</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td class="center" colspan="3"><?php echo $text_no_results; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>


	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3 center">
				<button class="btn btn-primary">
					<i class="fa fa-trash-o"></i> <?php echo $form['submit']->text; ?>
				</button>
			</div>
		</div>
	</div>
	</form>
</div><!-- <div class="tab-content"> -->

<script type="text/javascript">
	$('#checkall').click(function(){
		checkAll('selected', $(this).prop('checked'));
	});
	$('#clearall').click(function(){
		$('#checkall').prop('checked','checked');
		checkAll('selected', true);
		$('#cacheFrm').submit();
	});

</script>















