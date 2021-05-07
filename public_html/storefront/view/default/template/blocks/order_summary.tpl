<?php if ($products || $this->getHookVar('list_more_product_last')) { ?>
<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>

		<table style="width: 100%; border-spacing: 2px;">
			<?php foreach ($products as $product) { ?>
				<tr>
					<td class="align_left valign_top"><?php echo $product['quantity']; ?> x <a
								href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>

						<div>
							<?php foreach ($product['option'] as $option) { ?>
								-
								<small title="<?php echo $option['title']?>"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
								<br/>
							<?php } ?>
						</div>
						<?php echo $this->getHookVar('order_summary_product_'.$product['key'].'_additional_info_1'); ?>
					</td>
					<td class="align_right valign_top"><b><?php echo $product['price']; ?></b></td>
				</tr>
			<?php echo $this->getHookVar('order_summary_product_'.$product['key'].'_additional_info_2'); ?>
			<?php } ?>
			<?php echo $this->getHookVar('list_more_product_last'); ?>
		</table>
		<br/>
		<div class="gray_separator"></div>
		<table style="width: 100%; border-spacing: 0; padding: 0;">
			<?php foreach ($totals as $total) { ?>
				<tr>
					<td class="align_right"><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span>
					</td>
					<td class="align_right"><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
				</tr>
			<?php } ?>
		</table>

	<?php if (!$products) { ?>
		 		<div class="align_center"><?php echo $text_empty; ?></div>
	<?php } ?>
</div>
<?php } ?>