<div class="sidewidt <?php echo $block_details['block_txt_id'] ?>">
	<div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
		 id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
		<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
			<table>
				<tbody>
				<?php if ($products) { ?>
					<?php foreach ($products as $product) { ?>
						<tr>
							<td class="image"><a href="<?php echo $product['href']; ?>">&nbsp;
									<img src="<?php echo $product['thumb']['thumb_url']; ?>"
										 alt="product" title="product"></a></td>
							<td class="name"><a
										href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>

								<div>
									<?php foreach ($product['option'] as $option) { ?>
										-
										<small title="<?php echo $option['title']?>"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
										<br/>
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
			<table class="totals pull-right">
				<tbody>
				<?php foreach ($totals as $total) { ?>
					<tr>
						<td><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
						<td><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<div class="well buttonwrap col-sm-12 col-md-12">
			<a class="btn btn-orange btn-xs pull-left" href="<?php echo $view; ?>"><i
						class="fa fa-shopping-cart fa-fw"></i> <?php echo $text_view;?></a>
			<a class="btn btn-orange btn-xs pull-right"
			   href="<?php echo $checkout; ?>"><i class="fa fa-pencil fa-fw"></i>  <?php echo $text_checkout; ?></a>
		</div>
	</div>
</div>

