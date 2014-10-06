<?php if ($error_warning) { ?>
<div class="alert alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php if ($preview_id) { ?>
<div class="alert alert-info"><?php echo $text_preview_generated; ?> <a href="<?php echo $preview_url; ?>" target="_blank"><?php echo $text_click_here; ?></a></div>
<?php } ?>

<?php
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
?>

<?php echo $form_begin; ?>
<div class="row">
  <div class="col-sm-12 col-lg-12">
    <ul class="content-nav">
      <li>
        <?php echo $text_select_template; ?>
        <div class="btn-group">
          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fa fa-folder-o"></i>
            <?php echo $tmpl_id; ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <?php echo $template_list; ?>
          </ul>
        </div>
      </li>
      <li>
        <div class="btn-group">
          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fa fa-square-o"></i>
            <?php echo $current_page['name']; ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <?php echo $page_list; ?>
          </ul>
        </div>
      </li>
      <li>
        <a class="actionitem" href="<?php echo $current_url; ?>"><i class="fa fa-refresh"></i> Reload</a>
      </li>
      <li>
        <a class="actionitem layout-form-save" href="<?php echo $page_url; ?>"><i class="fa fa-save"></i> Save</a>
      </li>
      <li>
        <a class="actionitem layout-form-preview" href="<?php echo $generate_preview_url; ?>"><i class="fa fa-search"></i> Preview</a>
      </li>
      <?php if ($current_ok_delete) { ?>
      <li>
        <a class="actionitem delete_page_layout" href="<?php echo $page_delete_url; ?>"><i class="fa fa-trash-o"></i> Delete current page layout</a>
      </li>
      <?php } ?>
    </ul>
  </div>
</div>

<div id="page-layout" class="container-fluid">
  <?php echo $layoutform; ?>
  <?php echo $hidden_fields; ?>
</div>

</form>

<script type="text/javascript"><!--

$('.delete_page_layout').click(function(e) {
  e.stopPropagation();
  e.preventDefault();
  
  if (confirm('<?php echo $text_delete_confirm; ?>' )) {
    var url = $(this).attr('href');
    window.location = url + '&confirmed_delete=yes';  
  }
});

--></script>