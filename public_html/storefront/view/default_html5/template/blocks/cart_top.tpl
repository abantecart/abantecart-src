<ul class="nav topcart pull-left">
    <li class="dropdown hover carticon">
        <a href="<?php echo $view; ?>" class="dropdown-toggle"><?php /* echo $heading_title; */?>&nbsp;&nbsp;<span
            class="label label-orange font14"><?php echo $total_qty;?></span> <?php echo $text_items;?> - <span
            class="cart_total"><?php echo $subtotal; ?></span> <b class="caret"></b></a>
        <ul class="dropdown-menu topcartopen ">
            <li>
		<span id="top_cart_product_list">
			<table>
                <tbody>
                <?php if ($products) { ?>
                    <?php foreach ($products as $product) { ?>
                    <tr>
                        <td class="image" ><a href="<?php echo $product['href']; ?>">&nbsp;
                            <img src="<?php echo $product['thumb']['thumb_url']; ?>"
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
			<table class="totals pull-right mr20">
                <tbody>
                <?php foreach ($totals as $total) { ?>
                <tr>
                    <td><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
                    <td><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
                </tr>
                    <?php } ?>
                </tbody>
            </table>
		</span>
                <div class="well buttonwrap span3">
                    <a class="btn btn-orange pull-left" href="<?php echo $view; ?>"><i class="icon-shopping-cart icon-white"></i> <?php echo $text_view;?></a>&nbsp;&nbsp;
                    <a class="btn btn-orange pull-right"
                       href="<?php echo $checkout; ?>"><i class="icon-pencil icon-white"></i>  <?php echo $text_checkout; ?></a>
                </div>
            </li>
        </ul>
    </li>
</ul>