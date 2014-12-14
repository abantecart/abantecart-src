<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<button class="btn btn-primary lock-on-click" id="clearall" type="button"><i class="fa fa-trash-o"></i> <?php echo $text_clear_all_cache;?></button>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

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

	<div class="panel-footer col-xs-12">
		<div class="text-center">
				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-trash-o"></i> <?php echo $form['submit']->text; ?>
				</button>
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















