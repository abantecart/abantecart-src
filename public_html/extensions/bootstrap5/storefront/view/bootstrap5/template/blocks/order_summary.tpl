<div id="fast_checkout_summary_block" class=" mt-3">
    <div id="cart_details d-flex flex-column mx-sm-auto">
<?php   if ($products || $this->getHookVar('list_more_product_last')) { ?>
        <h2>
            <?php echo $this->language->get('heading_title', 'blocks/order_summary');
            if ($cart_weight){
                echo '  ('.$cart_weight.')';
            } ?>
        </h2>
        <div class="products">
            <table class="table table-hover table-borderless">
                <tbody>
                <?php
                foreach ($products as $product) { ?>
                    <tr>
                        <td class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-none d-md-flex mx-1">
                                <img alt="" class="product-icon" src="<?php echo $product['thumbnail']['main_url']; ?>">
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
                            <?php echo $this->getHookVar('order_summary_product_'.$product['key'].'_additional_info_1'); ?>
                        </td>
                    </tr>
                    <?php echo $this->getHookVar('order_summary_product_'.$product['key'].'_additional_info_2'); ?>
                <?php }  //end foreach
                echo $this->getHookVar('list_more_product_last'); ?>
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
        <?php } ?>
    </div>
</div>
