<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>

	<?php if ($products || $this->getHookVar('list_more_product_last')) { ?>
		<table cellpadding="2" cellspacing="0" style="width: 100%;">
			<?php foreach ($products as $product) { ?>
				<tr>
					<td align="left" valign="top"><?php echo $product['quantity']; ?> x <a
								href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>

						<div>
							<?php foreach ($product['option'] as $option) { ?>
								-
								<small style="color: #999;"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
								<br/>
							<?php } ?>
						</div>
					</td>
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
	<?php } ?>
	<?php if ($products) { ?>
		<div class="buttonwrap span3">
			<a class="btn btn-orange pull-right" href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a>
		</div>

	<?php } else { ?>
		<div style="text-align: center;"><?php echo $text_empty; ?></div>
	<?php } ?>
