<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $this->language->get('text_popup_title'); ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"></label>

		<?php
		if ($dataset_info) {
			$response = '';
			foreach ($dataset_info as $key => $info) {
				if (!is_array($info)) {
					$response .= '<dl class="dl-horizontal"><dt>' . $this->language->get('text_' . $key) . '</dt><dd>' . $info . '</dd></dl>';
				} else {
					if ($info) {
						$response .= '<dl class="dl-horizontal"><dt>' . $this->language->get('text_' . $key) . '</dt><dd></dd></dl>';
						foreach ($info as $info_name => $info_value) {
							if (!is_array($info_value)) {
								if ($info_name == 'controller') {
									$info_value = '<a href="' . $this->html->getSecureURL($info_value, '&dataset_id=' . $this->request->get[ 'dataset_id' ]) . '" title="review">' . $info_value . '</a>';
								}
								$response .= '<dl class="dl-horizontal"><dt></dt><dd>' . $info_name . ': ' . $info_value . '</dd></dl>';
							} else {
								foreach ($info_value as $k => $v) {
									$response .= '<dl class="dl-horizontal"><dt></dt><dd>' . $k . ': ' . $v . '</dd></dl>';
								}

							}
						}
					}
				}
			}
			echo $response;

		}

		?>
	</div>

</div>

