<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
  </div>
  
  <div class="tab-content">
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
	
		<div class="form-group">
		<?php
		$layouts = array();
		if ($block_info) {
			$tmp = '';
			foreach ($block_info as $row) {

				if ($tmp == $row['template_id'] . '-' . $row['page_id'] . '-' . $row['layout_id']) {
					continue;
				} else {
					$tmp = $row['template_id'] . '-' . $row['page_id'] . '-' . $row['layout_id'];
				}
				$row['templates'] = explode(',', $row['templates']);
				unset($row['layout_id'], $row['layout_name'], $row['page_id'], $row['template_id'], $row['store_id']);
				$info = $row;
			}	

			foreach($blocks_layouts as $row){
				$info['layouts'][] = '<a target="_blank" href="' . $this->html->getSecureURL('design/layout', '&tmpl_id=' . $row['template_id'] . '&page_id=' . $row['page_id'] . '&layout_id=' . $row['layout_id']) . '">' . $row['layout_name'] . '</a>';
			}
			if(!$info['layouts']){
				$layouts = array($text_none);
			}

			foreach ($info as $key => $item) {
				if (!is_array($item)) {
					echo  '<dl class="dl-horizontal"><dt>' . $this->language->get('text_'.$key).'</dt><dd>'.$item.'</dd></dl>';
				} else {
					if ($item) {
						echo '<dl class="dl-horizontal"><dt>' . $this->language->get('text_' . $key) . ':</dt><dd></dd></dl>';
						foreach ($item as $info_name => $info_value) {
							if (!is_array($info_value)) {
								echo '<dl class="dl-horizontal"><dt></dt><dd>' . $info_value . '</dd></dl>';
							} else {
								foreach ($info_value as $v) {
									echo '<dl class="dl-horizontal"><dt></dt><dd>' . $v . '</dd></dl>';
								}

							}
						}
					}
				}
			}

		}

		?>
		</div>
		<div class="form-group">
		   <?php echo $description; ?>
		</div>
		
  	</div>
  	
	<div class="panel-footer col-xs-12">
		<div class="center">
		<?php if($allow_edit) { ?>
			<a class="btn btn-primary lock-on-click" href="<?php echo $block_edit; ?>" target="_new">
			<i class="fa fa-edit fa-fw"></i> <?php echo $text_edit; ?>
			</a>
		<?php } ?>

			<button class="btn btn-default" data-dismiss="modal">
				<i class="fa fa-times fa-fw"></i> <?php echo $text_close; ?>
			</button>
		</div>
	</div>
  	
  </div>
</div>
