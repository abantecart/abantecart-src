<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="offer2_title" class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $title; ?></h4>
</div>

<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div id="offer2_content" class="panel-body panel-body-nopadding">
		<?php echo $html; ?>
	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="center">
				<?php if (!empty ($help_url)) { ?>
				<div class="btn-group">
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip" data-original-title="<?php echo $text_external_help; ?>">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				</div>
				<?php } ?>

				<?php if ($back) { ?>
				<div class="btn-group">
					<a class="btn btn-white step_back" href="<?php echo $back; ?>">
						<i class="fa fa-arrow-left"></i> <?php echo $button_back; ?>
					</a>
				</div>
				<?php } ?>
				<button id="offer2_next" class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $text_next; ?> <i class="fa fa-arrow-right"></i>
				</button>
			</div>
		</div>
	</div>
	</form>
</div>

<?php include('quick_start_js.tpl'); ?>