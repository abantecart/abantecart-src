<div class="tab-content">
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
?>
</div>


