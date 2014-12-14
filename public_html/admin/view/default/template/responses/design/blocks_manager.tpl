<div class="blocks-manager">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $text_close; ?></span></button>
    <h4 class="modal-title"><?php echo $text_add_block; ?></h4>
  </div>
  <div class="modal-body">
    <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#blocks" role="tab" data-toggle="tab"><?php echo $text_available_block; ?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="blocks">
        <ul class="blocks-list">
          <?php foreach ($blocks as $block) { ?>
          <li>
            <a class="block-item" data-id="<?php echo $block['id']; ?>" data-add-url="<?php echo $addBlock; ?>">
              <i class="fa fa-square-o pull-left"></i>
              <?php if ($block['custom_block_id'] > 0) { ?>
              <span class="title"><?php echo $block['block_name']; ?></span>
              <span class="info">(<?php echo $block['block_txt_id']; ?>)</span>
              <?php } else { ?>
              <span class="title"><?php echo $block['block_txt_id']; ?></span>
              <span class="info"></span>
              <?php } ?>
            </a>
          </li>
          <?php } ?>
        </ul>
      </div>
      <!-- <div class="tab-pane" id="create-block">...</div> -->
    </div>
  </div>
</div>