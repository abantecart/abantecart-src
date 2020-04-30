<div id="cart_details">
    <?php if (is_array($products) && count($products) > 0) { ?>
	<ul class="list-group">
		<li class="list-group-item active">
			<h4 class="list-group-item-heading">
				<?php echo $this->language->get('fast_checkout_order_summary'); ?>
				<span class="pull-right">
                        <?php echo $total_string; ?>
                      </span>
			</h4>
		</li>
        <?php foreach ($products as $p) { ?>
			<li class="list-group-item">
				<table class="table table-borderless">
					<tr>
						<td style="width:<?php echo $this->config->get('config_image_grid_width'); ?>px">
							<img src="<?php echo $p['thumbnail']['main_url'] ?>"
								 style="width:<?php echo $this->config->get('config_image_grid_width'); ?>px; height:auto;"/></td>
						<td style="text-align: left"><?php echo $p['name'] ?>
                            <?php if ($p['option'] && is_array($p['option'])) { ?>
							<p class="list-group-item-text">

							<ul class="product_option_list">
                                <?php foreach ($p['option'] as $option) { ?>
									<li>

                                        <?php if ($option['title']) { ?>
											<span class="pull-left">
                            <small><b><?php echo $option['title'] ?></b></small>
                            </span>
                                        <?php } ?>
										<small><b><?php echo $option['name']; ?>:</b> <?php echo $option['value']; ?></small>
									</li>
                                <?php } ?>
							</ul>
							</p>
                    <?php } ?>
						</td>
						<td style="text-align: right; font-weight: bold;"><?php echo $p['price']."&nbsp;x&nbsp;".$p['quantity']; ?></td>
					</tr>
				</table>
			</li>
        <?php } ?>

        <?php
        foreach ($totals as $ttl) {
            ?>
			<li class="list-group-item">
                <?php
                if ($ttl['id'] == 'total') {
                    $ttl['title'] = "<b>".$ttl['title']."</b>";
                    $ttl['text'] = "<b>".$ttl['text']."</b>";
                }
                ?>
				<span class="badge"><?php echo $ttl['text']; ?></span>
				<h4 class="list-group-item-heading"><?php echo $ttl['title']; ?></h4>
			</li>
        <?php } ?>
	</ul>
    <?php } ?>
</div>

<script>
	$('#cart_details').on('reload', function () {
		alert('reload please');
	});
</script>
