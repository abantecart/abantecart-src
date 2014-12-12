<?php
$statusClass = ( $status && $template_availability ) ? '' : 'off';
$blockValue = $blockId . '_' . $customBlockId;
$blockName = $customBlockId ? $customName . ' <span>(' . $name . ')</span>' : $name;
?>
<div class="block <?php echo $statusClass; ?>" data-instance-id="<?php echo $id; ?>" data-validate-url="<?php echo $validate_url; ?>">
  <div class="block-content">
    <div class="block-title"><?php echo $blockName; ?></div>
    <div class="block-options">
<?php if ($editUrl && $customBlockId) { ?>    
      <a class="button" href="<?php echo $editUrl; ?>" target="_new" data-toggle="tooltip" data-placement="right" title="<?php echo $text_edit; ?>"><i class="fa fa-cog"></i></a>
<?php } ?>      
<?php if (has_value($blockId) || has_value($customBlockId)) { ?>        
      <a class="button blk-switch" data-toggle="tooltip" data-placement="right" title="<?php echo $text_enable; ?>"><i class="fa fa-power-off"></i></a>
      <a class="button blk-info" data-info-block="<?php echo $block_info_url; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $text_details; ?>"><i class="fa fa-info-circle"></i></a>
      <a class="button blk-delete pull-right" data-toggle="tooltip" data-placement="left" title="<?php echo $text_delete; ?>"><i class="fa fa-trash-o"></i></a>
<?php } ?>        
    </div>
  </div>
  <div class="afield">
  <input class="block-id" type="hidden" name="block[]" value="<?php echo $blockValue; ?>">
  <input class="block-status" type="hidden" name="blockStatus[]" value="<?php echo $status; ?>">
  <input class="block-parent" type="hidden" name="parentBlock[]" value="<?php echo $parentBlock; ?>">
  </div>
</div>