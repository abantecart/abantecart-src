<?php
	$class_name = ''; 
	if (empty ($parent['id'] )) {
	  $class_name = "_parent";
	}
?>
<div class="postit_notes_box<?php echo $class_name; ?>">
  <span onClick="$('#postit<?php echo $id; ?>').toggleClass('show');" class="postit_icon" title="<?php echo $text_click; ?>"></span>
  <div id="postit<?php echo $id; ?>" class="postit_notes">
    <a class="postit_close" onClick="$('#postit<?php echo $id; ?>').removeClass('show');" title="<?php echo $text_close; ?>"><span class="icon_close"></span></a>
    <ul>
      <li><?php echo $text_block_id . ' ' . $id; ?></li>
      <li><?php echo $text_block_name; ?> <b><?php echo $name; ?></b></li>
      <li><?php echo $text_block_controller . ' ' . $controller; ?></li>
      <li><?php echo $text_block_path . ' ' . $controller_path; ?></li>
      <li><?php echo $text_block_template . ' ' . $tpl_path; ?></li>
      <li>
        <?php echo $text_block_parent; ?> <a class="icon_xpandarrow" onClick="$('#postit_parent<?php echo $id; ?>').toggleClass('expand'); $(this).toggleClass('expand');"><?php echo $parent_block; ?></a>
        <ul id="postit_parent<?php echo $id; ?>" class="collapse">
          <li><?php echo $text_parent_id . ' ' . $parent['id']; ?></li>
          <li><?php echo $text_block_controller . ' ' . $parent['controller']; ?></li>
          <li><?php echo $text_block_path . ' ' . $parent['controller_path']; ?></li>
          <li><?php echo $text_block_template . ' ' . $parent['tpl_path']; ?></li>
        </ul>
      </li>
    </ul>
  </div>
</div>