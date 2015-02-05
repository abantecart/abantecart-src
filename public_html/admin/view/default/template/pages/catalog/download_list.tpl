<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="actionitem btn btn-primary tooltips"  href="<?php echo $button_insert->href; ?>"
					data-target="#download_modal" data-toggle="modal" title="<?php echo $button_add; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
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
							<button type="submit"
									class="btn btn-xs btn-primary"><?php echo $search_form['submit']->text ?></button>
							<button type="reset" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i></button>
						</div>
					</form>
				<?php
				}
				?>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'download_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax',
				'js_onload' => "$('#downloadFrm_activate').change(); bindCustomEvents('#downloadFrm');"
		));
?>

<script type="text/javascript">
	var grid_ready = function(){
		$('.grid_action_edit[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#download_modal');
		});
	}
</script>
