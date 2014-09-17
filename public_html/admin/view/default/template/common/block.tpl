<?php
$statusClass = $status ? '' : 'off';
$blockValue = $blockId . '_' . $customBlockId;
$blockName = $customBlockId ? $customName . ' <span>(' . $name . ')</span>' : $name;
?>
<div class="block <?php echo $statusClass; ?>" data-instance-id="<?php echo $id; ?>">
  <div class="block-content">
    <div class="block-title"><?php echo $blockName; ?></div>
    <div class="block-options">
      <a class="button blk-config" data-toggle="tooltip" data-placement="right" title="Block configs"><i class="fa fa-cog"></i></a>
      <a class="button blk-switch" data-toggle="tooltip" data-placement="right" title="Enable/Disable Block"><i class="fa fa-power-off"></i></a>
      <a class="button blk-info"><i class="fa fa-info-circle"></i></a>
      <a class="button blk-delete pull-right" data-toggle="tooltip" data-placement="left" title="Delete Block"><i class="fa fa-trash-o"></i></a>
    </div>
  </div>
  <input class="block-id" type="hidden" name="block[]" value="<?php echo $blockValue; ?>">
  <input class="block-status" type="hidden" name="blockStatus[]" value="<?php echo $status; ?>">
  <input class="block-parent" type="hidden" name="parentBlock[]" value="<?php echo $parentBlock; ?>">
</div>