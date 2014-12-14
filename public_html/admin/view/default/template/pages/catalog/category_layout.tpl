<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $category_tabs ?>

<?php
$template_list = '';
foreach ($templates as $template) {
  $item_class = '';
  if ($tmpl_id == $template) {
    $item_class = ' class="disabled"';
  }
  $template_list .= '<li' . $item_class . '><a href="' . $page_url . '&tmpl_id=' . $template . '">' . $template . '</a></li>';    
}

?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			  <button class="btn btn-default dropdown-toggle tooltips" type="button" data-toggle="dropdown" title="<?php echo $text_select_template; ?>">
			    <i class="fa fa-photo"></i>
			    <?php echo $tmpl_id; ?> <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
			    <?php echo $template_list; ?>
			  </ul>
			</div>

			<div class="btn-group toolbar">
				<button class="actionitem btn btn-primary lock-on-click layout-form-save tooltips" title="<?php echo $button_save; ?>">
					<i class="fa fa-save fa-fw"></i>
				</button>
			</div>

			<div class="btn-group mr10 toolbar">
				<a class="actionitem btn btn-default lock-on-click tooltips" href="<?php echo $current_url; ?>" title="<?php echo $button_reset; ?>">
					<i class="fa fa-refresh fa-fw"></i>
				</a>
			</div>

			<div class="btn-group mr10 toolbar">
			<?php echo $cp_layout_frm; ?>
				<?php echo $hidden_fields; ?>
				<div class="form-group">
					<div class="input-group input-group-sm">
					<?php echo $cp_layout_select; ?>
					</div>
				</div>
				<div class="form-group">
					<div class="input-group input-group-sm">
					<button class="btn btn-default btn-xs lock-on-click tooltips" type="submit" title="<?php echo $text_apply_layout; ?>">
						<i class="fa fa-copy fa-fw"></i>
					</button>
					</div>
				</div>
			</form>
			</div>
			
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form_begin; ?>
	<div id="page-layout" class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $layoutform; ?>
		<?php echo $hidden_fields; ?>
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $button_save; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $current_url; ?>">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</a>
		</div>
	</div>
	</form>

</div>