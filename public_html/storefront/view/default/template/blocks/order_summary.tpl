<div class="s_block" id="block_order_summary">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/icon_summary.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">
            	<div class="category_list">


    <?php if ($products || $this->getHookVar('list_more_product_last')) { ?>
    <table cellpadding="2" cellspacing="0" style="width: 100%;">
      <?php foreach ($products as $product) { ?>
      <tr>
        <td align="left" valign="top"><?php echo $product['quantity']; ?> x <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <div>
            <?php foreach ($product['option'] as $option) { ?>
            - <small style="color: #999;"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
            <?php } ?>
          </div></td>
		<td align="right" valign="top"><b><?php echo $product['price']; ?></b></td>
      </tr>
      <?php } ?>
	  <?php echo $this->getHookVar('list_more_product_last'); ?>
    </table>
	<br/>
    <div class="gray_separator"></div>
    <table cellpadding="0" cellspacing="0" width="100%">
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td align="right"><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
        <td align="right"><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
      </tr>
      <?php } ?>
    </table>

    <?php if ($checkout) { ?>
    <div class="gray_separator"></div>
	<div align="center">
		<?php echo $this->getHookVar('pre_cart_buttons'); ?>
		<a href="<?php echo $checkout; ?>" class="btn_standard"><span class="button1"><span><?php echo $text_checkout; ?></span></span></a>
		<?php echo $this->getHookVar('post_cart_buttons'); ?>
	</div>
    <?php } ?>

    <?php } else { ?>
    <div style="text-align: center;"><?php echo $text_empty; ?></div>
    <?php } ?>
                    
            	</div>
            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>                    