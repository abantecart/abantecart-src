<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

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

    <?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $text_tables; ?></label>

		<div class="table-responsive">
		<?php foreach ($form['fields'] as $name => $field){ ?>
		    <table id="<?php echo $name.'_list'; ?>" class="table table-striped table-condensed list dropdown_list">
		    <thead>
		      <tr class="info">
		      	<td class="left">
		        	<?php if( !empty($field['children']) ) { ?>
		        	<a href="javascript:void(0);" class="open_close add">
		        		<i class="fa fa-expand fa-fw"></i>
		        	</a>
		        	<?php } else { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
				</td>
				
		      	<td class="left">
		        	<input type="hidden" class="is_checked" name="<?php echo 'data['.$name.'][is_checked]'; ?>" value="0" />
		        	<?php echo $field['main']; ?>
		      	</td>
		
		      	<td class="left col-sm-3">
		        	<label for="<?php echo 'exportFrm_section_'.$name; ?>" value="<?php echo $name; ?>"><?php echo ucfirst($name); ?></label>
		      	</td>
		      	<td class="left form-inline">
		      		<label class="control-label"><?php echo $text_range_from; ?></label>
		      		<div class="input-group input-group-sm afield col-sm-2">
		      			<input class="form-control atext small-field" type="text" name="<?php echo 'data['.$name.'][start_id]'; ?>">
		      		</div>
		      		<label class="control-label"><?php echo $text_to; ?></label>
		      		<div class="input-group input-group-sm afield col-sm-2">
		      			<input class="form-control atext small-field" type="text" name="<?php echo 'data['.$name.'][end_id]'; ?>">
		      		</div>
		        </td>
		        <td class="left form-inline col-sm-4">
		        	<?php if( isset($field['filter']) ) { ?>
		        		<label class="control-label"><?php echo $text_sort_by; ?></label>
		        		<div class="input-group input-group-sm afield">
		        		<?php echo $field['filter']['columns']; ?>
		        		</div>
		        	<?php } ?>
		        </td>
		      </tr>
		    </thead>
		      <?php if( !empty($field['children']) ) { ?>
		        <?php foreach ( $field['children'] as $tableName => $table ) { ?>
		        	<tr>
		      			<td class="left">
		      			</td>
		        		<td class="center">
		        			<?php echo $table['field']; ?>
		        		</td>
		        		<td class="left">
		        			<label for="<?php echo 'exportFrm_'.$table['name']; ?>"><?php echo $tableName; ?></label>
		        		</td>
		        		<td></td>
		        		<td></td>
		        	</tr>
		        <?php } ?>
		      <?php } ?>
		    </table>
		<?php } ?>
		</div>

		<label class="h4 heading"><?php echo $text_options; ?></label>

		<?php foreach ( $form['options']['item'] as $optionName => $option ){ ?>
		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12"><?php echo $form['options']['text'][$optionName]; ?></label>
			<div class="input-group afield">
				<?php echo $option; ?>
			</div>
		</div>		
		<?php } ?>
	  
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary">
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

	$('.open_close').click(function (event)
	{
		var table = $(this).closest('table').find('tbody');
		if ( $(table).is(':visible') )
		{
			$(table).fadeOut();
			$(this).removeClass('remove').addClass('add');
		}
		else
		{
			$(table).fadeIn();
			$(this).removeClass('add').addClass('remove');
		}
	});

	$('.section_input').click(function ()
	{
		if ( $(this).is(':checked') )
		{
			$(this).closest('td').find('input.is_checked').val(1);
			$(this).closest('table.list').find('tbody input[type=checkbox]').attr('checked', 'checked');
			$(this).closest('table.list').find('.acheckbox').addClass('checked');
		}
		else
		{
			$(this).closest('td').find('input.is_checked').val(0);
			$(this).closest('table.list').find('tbody input[type=checkbox]').removeAttr('checked');
			$(this).closest('table.list').find('.acheckbox').removeClass('checked');
		}

	});

	$(document).ready(function()
	{
		$('table.list tbody').hide();
		$('.aform').show();
	});

</script>