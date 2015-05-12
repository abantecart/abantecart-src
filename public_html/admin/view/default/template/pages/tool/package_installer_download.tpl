<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="tab-content">
	<div class="panel-heading">
		<label class="h4 heading"><?php echo $heading_title; ?></label>

		<div class="primary_content_actions pull-left"></div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open'] . (is_array($form['hidden']) ? implode('',$form['hidden']) : $form['hidden']); ?>
	<div class="panel-body panel-body-nopadding">
	<?php if ($loading) { ?>
		<div id="loader" class="col-sm-7 col-sm-offset-2 center">
			<div id="progress"
				<div class="progress">
					<div class="progress-bar progress-bar-striped active"
						 style="width:100%"
						 role="progressbar"
						 aria-valuenow="100"
						 aria-valuemin="0"
						 aria-valuemax="100"></div>
				</div>
			<div class="form-group"><?php echo $loading; ?></div>
		</div>
		<div id="retry" class="text-center hide">
			<div class="warning alert alert-danger">
				<i class="fa fa-info fa-fw"></i> <?php echo $text_download_error; ?>
			</div>
		</div>

	<?php } else { ?>
		<div id="progress">
			<div class="form-group"><?php echo $pack_info; ?></div>
		</div>
	<?php } ?>
	</div>

	<div class="panel-footer hide">
		<div class="row">
			<div class="text-center">
				<button id="agree_btn" class="btn btn-primary">
					<i class="fa fa-refresh"></i> <?php echo $text_reload; ?>
				</button>
				&nbsp;
				<?php if ($form['disagree_button']) { ?>
					<a class="btn btn-default"
					   href="<?php echo $form['disagree_button']->href; ?>"><?php echo $form['disagree_button']->text; ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
	</form>

	</div><!-- <div class="tab-content"> -->

<?php if ($loading) { ?>
	<script type="text/javascript">
		$(function () {
			$.ajax({  type: 'POST',
				url: '<?php echo $url; ?>&start=1',
				timeout: 240000,
				global: false,
				error: function () {
					$('#loader').hide();
					$('#retry, div.panel-footer').removeClass('hide');
				},
				success: function (data) {
					if (data == 100) {
						window.location = '<?php echo $redirect; ?>';
					} else {
						$('#loader').hide();
						$('#retry, div.panel-footer').removeClass('hide');
					}
				}
			});

		});

	</script>
<?php } ?>