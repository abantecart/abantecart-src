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

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_extension"><?php echo $title; ?></div>
	<div class="heading-tabs">
        <a href="<?php echo $link_export; ?>" <?php echo ( $active == 'export' ? 'class="active"' : ''  ) ?> ><span><?php echo $tab_export; ?></span></a>
        <a href="<?php echo $link_import; ?>" <?php echo ( $active == 'import' ? 'class="active"' : ''  ) ?> ><span><?php echo $tab_import; ?></span></a>
	</div>

	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	
	</div>

  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<h2><?php echo $form_title; ?></h2>
          
          <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>

          <?php if ( isset($file_field) ): ?>
          	<?php echo $file_form_open; ?>
			  <div class="fieldset">
				<div class="heading">
					<a id="file_heading"><?php echo $text_load_file; ?></a>
				</div>
				<div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
				<div class="cont_left"><div class="cont_right"><div class="cont_mid">

					 <table class="form">
						<tr>
							<td>
								 <?php echo $text_file_field; ?>
							</td>
							<td>
								 <div style="width:300px;">
									<div class="flt_left">
										<?php echo $file_field; ?>
									</div>
									<div class="clr_both"></div>
									<?php echo $text_formats; ?>
								 </div>
							</td>
						</tr>
						<?php foreach ($options['item'] as $optionName => $option): ?>
						<tr>
							 <td>
								 <?php echo $options['text'][$optionName]; ?>
							 </td>
							 <td>
								 <?php echo $option; ?>
							 </td>
						</tr>
						<?php endforeach; ?>
					 </table>
					 
				</div></div></div>
				<div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
			  </div>

			  <div class="buttons align_center">
				  <a class="btn_standard" href="<?php echo ${'link_'.$active}; ?>" ><?php echo $form['cancel']; ?></a>
				  <button type="submit" class="btn_standard button_loader" id="file_submit" name="file_submit"><?php echo $form['submit']; ?></button>
			  </div>
			</form>

          <?php endif; ?>

          <?php echo $form['form_open']; ?>

          <?php if (isset($form['fields'])): ?>
          	<div class="fieldset">
          		<div class="heading">
					<a id="tables_heading"><?php echo $text_tables; ?></a>
				</div>
			<div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
			<div class="cont_left"><div class="cont_right"><div class="cont_mid">

          	<?php foreach ($form['fields'] as $name => $field): ?>
          		<table id="<?php echo $name.'_list'; ?>" class="list dropdown_list" style="margin: 5px 0;">
          			<thead>
					  <tr style="background: none repeat scroll 0 0 #FDF5CE;">
					  	<td class="left" style="width: 50px;">
							<input type="hidden" class="is_checked" name="<?php echo 'data['.$name.'][is_checked]'; ?>" value="0" />
							<a href="javascript:void(0);" class="open_close add" style="height:17px;"></a>&nbsp;
							<?php echo $field['main']; ?>
					  	</td>

					  	<td class="left" style="width: 147px;">
							<label for="<?php echo 'exportFrm_section_'.$name; ?>" value="<?php echo $name; ?>"><?php echo ucfirst($name); ?></label>
					  	</td>
					  	<td class="left" style="width: 280px;">

					  		<?php if ( $active == 'export' ) : ?>
					  		<?php echo $text_range_from; ?>
					  		<span class="text_element"><div class="aform"><div class="afield mask1"><div class="cl"><div class="cr">
								<div class="cc">
									<input class="atext small-field" type="text" value="" name="<?php echo 'data['.$name.'][start_id]'; ?>">
								</div>
							</div></div></div></div></span>
							<?php echo $text_to; ?>
							<span class="text_element"><div class="aform"><div class="afield mask1"><div class="cl"><div class="cr">
								<div class="cc">
									<input class="atext small-field" type="text" value="" name="<?php echo 'data['.$name.'][end_id]'; ?>">
								</div>
							</div></div></div></div></span>
							<?php endif; ?>
						</td>
						<td class="left">
							<?php if( isset($field['filter']) ): ?>
								<?php echo $text_sort_by . $field['filter']['columns']; ?>
							<?php endif; ?>
						</td>
					  </tr>
          			</thead>
					  <?php if( !empty($field['children']) ): ?>
          				<?php foreach ( $field['children'] as $tableName => $table ): ?>
							<tr>
								<td class="center" style="padding-left: 50px;">
									<?php echo $table['field']; ?>
								</td>
								<td class="left" style="padding-left: 30px;">
									<label for="<?php echo 'exportFrm_'.$table['name']; ?>"><?php echo $tableName; ?></label>
								</td>
								<td></td>
								<td></td>
							</tr>
						<?php endforeach; ?>
					  <?php else: ?>
					  	<tr>
					  		<td></td>
					  		<td>
					  			<span style="line-height: 25px;"><?php echo $text_no_children; ?></span>
					  		</td>
					  		<td></td>
					  		<td></td>
					  	</tr>
					  <?php endif; ?>
          			
          		</table>
          	<?php endforeach; ?>

			</div></div></div>

			<div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
			</div>

	  		<?php if ( $active == 'export' ) : ?>
				<div class="fieldset">
					<div class="heading">
						<a id="options_heading"><?php echo $text_options; ?></a>
					</div>
					<div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
					<div class="cont_left"><div class="cont_right"><div class="cont_mid">
						<table class="form">
						<?php foreach ( $form['options']['item'] as $optionName => $option ): ?>

							<tr>
								<td><?php echo $form['options']['text'][$optionName]; ?></td>
								<td><?php echo $option; ?></td>
							</tr>

						<?php endforeach; ?>
						</table>
					</div></div></div>
					<div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
				</div>
			<?php endif; ?>


			<div class="buttons align_center">
		      <a class="btn_standard" href="<?php echo ${'link_'.$active}; ?>" ><?php echo $form['cancel']; ?></a>
			  <button type="submit" class="btn_standard" id="submit" name="submit" value="<?php echo $active; ?>"><?php echo $form['submit']; ?></button>

			</div>
		  <?php endif; ?>

		  </form>


	      </div></div></div>
          
	<div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>

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
		$('table.list tbody').hide();
		$('.aform').show();
	});

</script>

<?php echo $resources_scripts ?>