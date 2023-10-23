<?php
include($tpl_common_dir . 'action_confirm.tpl');
/* preview development is not complete. future */
if ($preview_id) { ?>
<div class="alert alert-info">
    <?php echo $text_preview_generated; ?> <a href="<?php echo $preview_url; ?>" target="_blank"><?php echo $text_click_here; ?></a>
</div>
<?php }

$template_list = '';
foreach ($templates as $template) {
  $item_class = '';
  if ($tmpl_id == $template) {
    $item_class = ' class="disabled"';
  }
  $template_list .= '<li' . $item_class . '><a href="' . $page_url . '&tmpl_id=' . $template . '">' . $template . '</a></li>';    
}

$current_ok_delete = false;
$page_list = '';
foreach ($pages as $page) {
  $uri = '&tmpl_id=' . $tmpl_id . '&page_id=' . $page['page_id'] . '&layout_id=' . $page['layout_id'];

  $item_class = '';
  if ($page['page_id'] == $current_page['page_id'] && $page['layout_id'] == $current_page['layout_id']) {
    $item_class = ' class="disabled"';
    if (empty($page['restricted'])) {
      $page_delete_url = $page_delete_url . $uri;
      $current_ok_delete = true;
    }
  }
  $page_list .= '<li' . $item_class . '>';
  $page_list .= '<a href="' . $page_url . $uri . '" title="' . $page['name'] . '">' . $page['layout_name'] . '</a>';
  $page_list .= '</li>';
}
$page_list .= '<li><a id="create_new_layout" href="'. $new_layout_modal_url.'" data-target="#new_layout_modal" data-toggle="modal" 
class="btn" title="'.htmlspecialchars($text_create_new_layout, ENT_QUOTES, 'UTF-8').'"><strong><i class="fa fa-plus-square-o"></i>&nbsp;'.$text_create_new_layout.'</strong></a></li>';

echo $this->html->buildElement(
        [
            'type'        => 'modal',
            'id'          => 'new_layout_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax',
            'js_onclose'  => ''
        ]
    );
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

			<div class="btn-group mr10 toolbar">
			  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
			    <i class="fa fa-square-o"></i>
			    <?php echo $current_page['layout_name']; ?> <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
			    <?php echo $page_list; ?>
			  </ul>
			</div>

			<div class="btn-group toolbar">
				<button class="actionitem btn btn-primary lock-on-click layout-form-save tooltips" title="<?php echo $button_save; ?>">
					<i class="fa fa-save fa-fw"></i>
				</button>
			</div>

			<div class="btn-group toolbar">
				<a class="actionitem btn btn-default lock-on-click tooltips" href="<?php echo $current_url; ?>" title="<?php echo $button_reset; ?>">
					<i class="fa fa-refresh fa-fw"></i>
				</a>
			</div>

        <?php if ($current_ok_delete) { ?>
			<div class="btn-group toolbar">
				<a class="actionitem btn btn-default delete_page_layout tooltips" href="<?php echo $page_delete_url; ?>" title="<?php echo $button_delete; ?>">
					<i class="fa fa-trash-o fa-fw"></i>
				</a>
			</div>
        <?php } ?>
        </div>
<?php
        include($tpl_common_dir.'content_buttons.tpl'); ?>
    </div>

<?php
    echo $form_begin; ?>
    <div id="page-layout" class="panel-body panel-body-nopadding tab-content col-xs-12">
        <?php
            echo $layout_form;
            echo $hidden_fields;
        ?>
    </div>
    </form>

</div>



<script type="text/javascript">
    $('.delete_page_layout').click(function (e) {
        e.stopPropagation();
        e.preventDefault();

        if (confirm(<?php js_echo($text_delete_confirm); ?>)){
            let url = $(this).attr('href');
            window.location = url + '&confirmed_delete=yes';
        }
    }
);
</script>