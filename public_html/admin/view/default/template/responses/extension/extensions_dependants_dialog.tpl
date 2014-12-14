<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_confirmation; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
	<?php
	foreach ($result as $child) {
		if ($this->config->get($child['key'] . '_status')) {
			if ($child['type'] == 'total') {
				$link = $this->html->getSecureURL('total/' . $child['key']);
			} else {
				$link = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $child['key']);
			}
			$children[] = '<a href="' . $link . '" target="_blank"><b>' . $child['key'] . '</b></a>';
		}
	}
	if ($children) {
	echo sprintf($text_confirm_disable_dependants, $this->request->get['extension'], '<br>' . implode('<br>', $children) . '<br>');
	}
?></div>
</div>
<div class="panel-footer">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 center">
			<a id="modal_confirm" class="btn btn-primary" >
				<i class="fa fa-check"></i> <?php echo $button_confirm; ?>
			</a>
			&nbsp;
			<a class="btn btn-default" data-dismiss="modal" href="#">
				<i class="fa fa-refresh"></i> <?php echo $button_cancel; ?>
			</a>
		</div>
	</div>
</div>