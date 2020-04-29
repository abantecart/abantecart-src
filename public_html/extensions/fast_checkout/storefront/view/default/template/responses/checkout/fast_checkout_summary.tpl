<div id="cart_details">
    <?php if (is_array($products) && count($products) > 0) { ?>
	<ul class="list-group">
		<li class="list-group-item active">
			<h4 class="list-group-item-heading">
				<i class="fa fa-shopping-cart fa-fw"></i>
				<span class="pull-right">
                        <?php echo $total_string; ?>
                      </span>
			</h4>
		</li>
        <?php foreach ($products as $p) { ?>
			<li class="list-group-item">
				<span class="badge"><?php echo $p['price']."  x  ".$p['quantity']; ?></span>
				<h4 class="list-group-item-heading">
					<a>
						<img src="<?php echo $p['thumbnail']['main_url'] ?>"
							 style="width:<?php echo $this->config->get('config_image_grid_width')
                                 ."px; height:".$this->config->get('config_image_grid_height'); ?>px;"/>
						<small><?php echo $p['name'] ?></small>
					</a>
				</h4>
				<p class="list-group-item-text">
                    <?php if ($p['option'] && is_array($p['option'])) { ?>
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
                <?php } ?>
				</p>
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
