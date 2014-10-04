<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $setting_tabs ?>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li>
				<?php
				if (!empty($search_form)) {
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
				<?php
				}
				?>
			</li>
			<li><a class="itemopt" title="<?php echo $insert->title; ?>" href="<?php echo $insert->href; ?>"><i	class="fa fa-plus-circle fa-lg"></i></a></li>
			<?php if (!empty ($form_language_switch)) { ?>
				<li>
					<?php echo $form_language_switch; ?>
				</li>
			<?php } ?>
			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
							<i class="fa fa-question-circle fa-lg"></i>
						</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php echo $listing_grid; ?>
			</div>
		</div>
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