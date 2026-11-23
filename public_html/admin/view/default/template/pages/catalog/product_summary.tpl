<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-btns">
            <a class="panel-close" href="">×</a>
            <a class="minimize" href="">−</a>
        </div>
        <h4 class="panel-title"><?php echo $text_product_summary; ?></h4>
    </div>
    <div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
        <table id="summary" class="table summary">
            <tr>
                <td class="summary_image" rowspan="3" align="center">
                    <?php echo $product['image']['thumb_html']; ?>
                </td>
                <td class="summary_label"><?php echo $entry_name; ?></td>
                <td class="summary_value"><?php echo $product['name']; ?></td>
                <td class="summary_label"><?php echo $entry_product_id; ?></td>
                <td class="summary_value"><?php echo $product['product_id']; ?></td>
                <td class="summary_label"><?php echo $entry_sku; ?></td>
                <td class="summary_value"><?php echo $product['sku']; ?></td>
            </tr>
            <tr>
                <td class="summary_label"><?php echo $entry_model; ?></td>
                <td class="summary_value"><?php echo $product['model']; ?></td>
                <td class="summary_label"><?php echo $entry_price; ?></td>
                <td class="summary_value"><?php echo $product['price']; ?></td>
                <td class="summary_label"></td>
                <td class="summary_value"></td>
            </tr>
            <tr>
                <td class="summary_label"><?php echo $text_product_condition; ?></td>
                <td class="summary_value"><?php
                    if ($product['condition']){
                        echo '<div class="alert-danger col-sm-8">' . implode('<br>', $product['condition']) . '</div>';
                    } else{
                        echo $text_product_available;
                    } ?></td>
                <td class="summary_label"><?php echo $text_total_orders; ?></td>
                <td class="summary_value">
                    <?php if ($product['orders'] > 0){ ?>
                        <a href="<?php echo $product['orders_url']; ?>"
                             class="btn btn-small btn-default"
                             target="_blank">
                            <?php echo $product['orders']; ?></a>
                    <?php }else{ 
                        echo $product['orders'];
                    } ?>
                </td>
                <td class="summary_label"><?php echo $this->language->get('entry_date_modified','sale/customer'); ?></td>
                <td class="summary_value"><?php echo  $product['date_modified']?></td>
            </tr>
            <?php
            $mentions = (array)$product['mentions'];
            $hasMentions = array_filter((array)$mentions['storefront']) || array_filter((array)$mentions['admin']);
            ?>
            <tr>
                <td class="summary_image" align="center">
                    <?php if ($hasMentions) { ?>
                        <a href="#product_mentions"
                           class="btn btn-small btn-default collapsed"
                           data-toggle="collapse"
                           role="button"
                           aria-expanded="false"
                           aria-controls="product_mentions"
                           title="<?php echo $text_product_mentions; ?>">
                            <i class="fa fa-list"></i> <?php echo $text_view; ?></a>
                    <?php } else { ?>
                        <a href="<?php echo $product['preview']; ?>"
                           class="btn btn-small btn-default"
                           target="_blank">
                            <i class="fa fa-external-link"></i> <?php echo $text_view; ?></a>
                    <?php } ?>
                </td>
                <td colspan="6">
                    <?php if ($hasMentions) { ?>
                    <div id="product_mentions" class="collapse">
                        <h4><?php echo $text_product_mentions; ?></h4>
                        <div class="row">
                        <?php foreach (['storefront', 'admin'] as $section) {
                            if (!array_filter((array)$mentions[$section])) { continue; } ?>
                            <div class="col-sm-6">
                                <h5><?php echo $this->language->get('text_mentions_' . $section); ?></h5>
                                <?php foreach ((array)$mentions[$section] as $group => $links) {
                                    if (!$links) { continue; } ?>
                                    <strong><?php echo $this->language->get('text_mention_' . $group); ?>:</strong>
                                    <ul class="list-unstyled ml10">
                                        <?php foreach ($links as $link) { ?>
                                            <li>
                                                <a href="<?php echo $link['url']; ?>" target="_blank">
                                                    <i class="fa fa-external-link"></i> <?php echo $link['title']; ?></a>
                                                <?php if ($link['seo_url']) { ?>
                                                    &nbsp;<a href="<?php echo $link['seo_url']; ?>" target="_blank" class="small"
                                                             title="<?php echo $link['seo_url']; ?>">[SEO]</a>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </td>
            </tr>
            <?php echo $this->getHookVar('product_summary_hookvar'); ?>
        </table>
    </div>
</div>
