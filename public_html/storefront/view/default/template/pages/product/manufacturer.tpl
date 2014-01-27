<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<div class="sort">
			<div class="div1"><?php echo $sorting; ?></div>
			<div class="div2"><?php echo $text_sort; ?></div>
		</div>
		<table class="list">
			<?php for ($i = 0; $i < sizeof($products); $i = $i + 4) { ?>
			<tr>
				<?php for ($j = $i; $j < ($i + 4); $j++) { ?>
				<td class="list_product"><?php if (isset($products[ $j ])) { ?>
					<a href="<?php echo $products[ $j ][ 'href' ]; ?>"><?php echo $products[ $j ][ 'thumb' ][ 'thumb_html' ]; ?></a>
					<br/>
					<a href="<?php echo $products[ $j ][ 'href' ]; ?>"><?php echo $products[ $j ][ 'name' ]; ?></a><br/>
					<span class="model"><?php echo $products[ $j ][ 'model' ]; ?></span><br/>
						<div class="price-add">
							<?php if ($display_price) { ?>
							<?php if (!$products[ $j ][ 'special' ]) { ?>
								<span class="price"><?php echo $products[ $j ][ 'price' ]; ?></span>
								<?php } else { ?>
								<span class="regular-price"><?php echo $products[ $j ][ 'price' ]; ?></span> <span
										class="special-price"><?php echo $products[ $j ][ 'special' ]; ?></span>
								<?php } ?>
							<?php } ?>
							<a class="info" href="<?php echo $products[ $j ][ 'href' ]; ?>"></a>
						<?php if(!$products[$j]['call_to_order']){ ?>

							<a class="buy" id="<?php echo $products[ $j ][ 'product_id' ]?>"
							   href="<?php echo $products[ $j ][ 'add' ]; ?>" title="<?php echo $button_add_to_cart; ?>"></a>
						<?php }else{ ?>
							<a href="#" class="call_to_order"><span class="price"><?php echo $text_call_to_order;?></span></a>
						<?php }?>
						</div>
					<br/>
					<?php echo $products[ $j ][ 'buttons' ]; ?>
					<?php if ($products[ $j ][ 'rating' ]) { ?>
						<img src="<?php echo $this->templateResource('/image/stars_' . $products[ $j ][ 'rating' ] . '.png'); ?>"
						     alt="<?php echo $products[ $j ][ 'stars' ]; ?>"/>
						<?php } ?>
					<?php } ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript"><!--

$('#sort').change(function () {
	Resort();
});

function Resort() {
	url = '<?php echo $url; ?>';
	url += '&sort=' + $('#sort').val();
	url += '&limit=' + $('#limit').val();
	location = url;
}
//--></script>