<table>
	<tbody>
	<?php if ($products) { ?>
		<?php foreach ($products as $product) { ?>
			<tr>
				<td class="image"><a href="<?php echo $product['href']; ?>"><img width="50"
																				 src="<?php echo $product['thumb']['thumb_url']; ?>"
																				 alt="product" title="product"></a></td>
				<td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>

					<div>
						<?php foreach ($product['option'] as $option) { ?>
							-
							<small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br/>
						<?php } ?>
					</div>
				</td>
				<td class="quantity">x&nbsp;<?php echo $product['quantity']; ?></td>
				<td class="total"><?php echo $product['price']; ?></td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>
<table>
	<tbody>
	<?php foreach ($totals as $total) { ?>
		<tr>
			<td align="right"><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
			<td align="right"><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>