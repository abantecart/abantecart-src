<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="actionitem btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>

			<div class="btn-group mr10 toolbar">
				<?php
				if (!empty($search_form)) {
					?>
					<form id="<?php echo $search_form['form_open']->name; ?>"
						  method="<?php echo $search_form['form_open']->method; ?>"
						  name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">

						<?php
						foreach ($search_form['fields'] as $f) {
							?>
							<div class="form-group">
								<div class="input-group input-group-sm">
									<?php echo $f; ?>
								</div>
							</div>
						<?php
						}
						?>
						<div class="form-group">
							<button type="submit" class="btn btn-xs btn-primary tooltips" title="<?php echo $button_filter; ?>">
									<?php echo $search_form['submit']->text ?>
							</button>
							<button type="reset" class="btn btn-xs btn-default tooltips" title="<?php echo $button_reset; ?>">
								<i class="fa fa-refresh"></i>
							</button>
						</div>
					</form>
				<?php
				}
				?>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<script type="application/javascript">
		$("#product_grid_go").on('click', function () {
			//get all selected rows based on multiselect
			var ids = $('#product_grid').jqGrid('getGridParam', 'selarrrow');
			//get single selected row
			ids.push($('#product_grid').jqGrid('getGridParam', 'selrow'));
			if (!ids.length) {
				return;
			}

			if ($('#product_grid_selected_action').val() == 'relate') {
				var form_data = $('#product_grid_form').serializeArray();
				form_data.push({name: 'id', value: ids});
				form_data.push({name: 'oper', value: 'relate'});
				$.ajax({
					url: '<?php echo $relate_selected_url; ?>',
					type: 'POST',
					data: form_data,
					success: function (msg) {
						if (msg == '') {
							jQuery('#product_grid').trigger("reloadGrid");
							success_alert('<?php js_echo($text_success_relation_set);?>',true);
						} else {
							alert(msg);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus + ": " + errorThrown);
					}
				});
			}
		});

</script>