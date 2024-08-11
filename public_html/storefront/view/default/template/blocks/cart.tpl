<?php if (is_array($products) && count($products) > 0) { ?>
<div class="mt-3 block-<?php echo $block_details['block_txt_id'] ?>">
    <div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
         id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
        <h2><?php echo $heading_title; ?></h2>
        <div id="cart_details d-flex flex-column mx-sm-auto">
            <div class="products">
                <table class="table table-hover table-borderless">
                    <tbody>
                    <?php
                    foreach ($products as $product) { ?>
                        <tr>
                            <td class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="d-none d-md-flex mx-1">
                                    <img alt="" class="product-icon" src="<?php echo $product['thumb']['thumb_url']; ?>">
                                </div>
                                <div class="ms-auto me-1 text-wrap">
                                    <?php if($product['href']){ ?>
                                        <a class="link-dark link-"  href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                                    <?php }else{
                                        echo $product['name'];
                                    }?>
                                    <div class="d-flex flex-column">
                                        <?php foreach ($product['option'] as $option) { ?>
                                            <small class="text-muted text-wrap" title="<?php echo $option['title']?>">
                                                - <?php echo $option['name']; ?> <?php echo $option['value']; ?>
                                            </small>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="ms-auto text-end">
                                    <?php echo $product['price']; ?>
                                    <span class="text-nowrap"><i class="mx-2 fa fa-times fa-fw"></i><?php echo $product['quantity']; ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <table class="table">
                <tbody>
                <?php foreach ($totals as $total) { ?>
                    <tr>
                        <th><?php echo $total['title']; ?></th>
                        <td><span class="float-end"><?php echo $total['text']; ?></span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-wrap justify-content-center ">
            <a class="btn btn-secondary btn-sm m-2" href="<?php echo $view; ?>">
                <i class="fa fa-shopping-cart "></i> <?php echo $text_view;?></a>
            <a class="btn btn-primary btn-sm m-2 "
               href="<?php echo $checkout; ?>">
                <i class="fa fa-pencil"></i>  <?php echo $text_checkout; ?></a>
        </div>
	</div>
</div>
<?php } ?>
