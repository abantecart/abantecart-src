<?php
$homeBlockId = 'viewed';
$block_framed = true;
$imgW = $this->config->get('viewed_products_image_width');
$imgH = $this->config->get('viewed_products_image_height');
include($this->templateResource('/template/blocks/product_cell_grid.tpl'));
?>