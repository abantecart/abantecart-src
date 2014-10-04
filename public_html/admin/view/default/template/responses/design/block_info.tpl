<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $text_block_info; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"></label>

		<?php
		$layouts = array();
		if ($block_info) {
			$tmp = '';
			foreach ($block_info as $row) {
				if ((int)$row['layout_id']) {
					$layouts[] = '<a target="_blank" href="' . $this->html->getSecureURL('design/layout', '&tmpl_id=' . $row['template_id'] . '&page_id=' . $row['page_id'] . '&layout_id=' . $row['layout_id']) . '">' . $row['layout_name'] . '</a>';
				}
				if ($tmp == $row['template_id'] . '-' . $row['page_id'] . '-' . $row['layout_id']) {
					continue;
				} else {
					$tmp = $row['template_id'] . '-' . $row['page_id'] . '-' . $row['layout_id'];
				}
				$row['templates'] = explode(',', $row['templates']);
				unset($row['layout_id'], $row['layout_name'], $row['page_id'], $row['template_id'], $row['store_id']);
				$info = $row;
			}
			if (!$layouts) {
				$layouts = array($text_none);
			}
			$info['layouts'] = $layouts;


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

</div>

