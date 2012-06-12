<div class="s_block" id="block_cart">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/basket.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">
            	<div class="category_list">

    <?php if ($products) { ?>
    <table cellpadding="2" cellspacing="0" style="width: 100%;">
      <?php foreach ($products as $product) { ?>
      <tr>
        <td align="left" valign="top" width="1"><span class="cart_remove" id="remove_<?php echo $product['key']; ?>">&nbsp;</span></td><td valign="top" align="right" width="1"><?php echo $product['quantity']; ?>&nbsp;x&nbsp;</td>
        <td align="left" valign="top"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <div>
            <?php foreach ($product['option'] as $option) { ?>
            - <small style="color: #999;"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
            <?php } ?>
          </div></td>
      </tr>
      <?php } ?>
    </table>
    <br />
    <table cellpadding="0" cellspacing="0" align="right" style="display:inline-block;">
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td align="right"><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
        <td align="right"><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
      </tr>
      <?php } ?>
    </table>
    <div style="padding-top:5px;text-align:center;clear:both;"><a href="<?php echo $view; ?>"><?php echo $text_view; ?></a> | <a href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></div>
    <?php } else { ?>
    <div style="text-align: center;"><?php echo $text_empty; ?></div>
    <?php } ?>
                    
<?php if ($ajax) { ?>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery/ajax_add.js'); ?>"></script>
<?php } ?>

<script type="text/javascript"><!--
jQuery(function ($) {
	$('.cart_remove').live('click', function () {
		if (!confirm('<?php echo $text_confirm; ?>')) {
			return false;
		}
        var id = this.id.replace('remove_','');
        var data = {remove:{}} ;
        data.remove[id] = 1;
		$(this).removeClass('cart_remove').addClass('cart_remove_loading');
		$.ajax({
			type: 'post',
			url: '<?php echo $remove ?>',
			data: data,
			success: function () {
				window.location.reload();
			}
		});
	});
});
//--></script>


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