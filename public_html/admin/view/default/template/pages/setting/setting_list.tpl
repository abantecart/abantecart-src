<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $setting_tabs ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" title="<?php echo $insert->title; ?>" href="<?php echo $insert->href; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
			<div class="btn-group mr10 toolbar">
			<?php if (!empty($search_form)) {
					?>
					<form id="<?php echo $search_form['form_open']->name; ?>"
						  method="<?php echo $search_form['form_open']->method; ?>"
						  name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">

							<div class="form-group">
								<label class="control-label"><?php echo $text_edit_store_settings; ?></label>
								<div class="input-group input-group-sm">
									<?php echo $search_form['fields']['store_selector']; ?>
								</div>
							</div>
					</form>
			<?php } ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'setting_modal',
				'name' => 'setting_modal',
				'modal_type' => 'lg',
				'content' => '',
				'title' => '',
		));
?>
<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_edit').click(

		function () {

			var href = $(this).attr('href');

			$.ajax({
				url:href,
				type:'GET',
				dataType:'json',
				success:function (data) {
					if (data == '' || data == null) {
						return null;
					} else {
						if (data.html) {
							$('#setting_modal .modal-body').html(data.html);
							$('#setting_modal .modal-title').html(data.title);
						}
						wrapCKEditor('add');
						$('#setting_modal').modal('show');
					}
				}
			});
			return false;
		});
	}

	$('#store_switcher').change(function(){
		goTo('<?php echo $store_edit_url;?>','store_id='+$(this).val());
	});

</script>
<?php if($resources_scripts){ echo $resources_scripts; } ?>