<table>
	<tbody>
	<?php if ($products) { ?>
		<?php foreach ($products as $product) { ?>
			<tr>
				<td class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']['thumb_url']; ?>"
																				 ></a></td>
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
<table class="pull-right mr20">
	<tbody>
	<?php foreach ($totals as $total) { ?>
		<tr >
			<td><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
			<td><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>