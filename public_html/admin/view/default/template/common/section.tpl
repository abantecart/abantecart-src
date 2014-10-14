<?php
$class = $name . ($status ? '' : ' off');
$sectionTitle = str_replace('_', ' ', $name);
?>
<div class="section <?php echo $class; ?>" data-instance-id="<?php echo $id; ?>" data-section-id="<?php echo $blockId; ?>">
  <div class="section-header">
    <span class="title"><?php echo $sectionTitle; ?></span>
    <a class="button sec-add-block" data-toggle="tooltip" data-placement="left" title="<?php echo $text_add_block; ?>" data-add-block="<?php echo $addBlockUrl;?>"><i class="fa fa-plus"></i></a><!--
    --><a class="button sec-switch" data-toggle="tooltip" data-placement="left" title="<?php echo $text_enable; ?>"><i class="fa fa-power-off"></i></a>
  </div>
  <div class="blocks clearfix">
    <?php echo $blocks; ?>
  </div>
  <input class="section-status" type="hidden" name="section[<?php echo $blockId; ?>][status]" value="<?php echo $status; ?>">
</div>