<div class="side_block">
    <?php if ($block_framed) { ?>
    <div class="block_frame block_frame_<?php echo $block_details['block_txt_id'];?>"
                 id="block_frame_<?php echo $block_details['block_txt_id'].'_'.$block_details['instance_id'] ?>">
        <h2><?php echo $heading_title; ?></h2>
    <?php }	?>

    <ul class="thumbnails">
<?php
if ($content) {
    $tax_exempt = $this->customer->isTaxExempt();
    $config_tax = $this->config->get('config_tax');
    foreach ($content as $item) {
        if (!$item['content_id']) {
            $item['title'] = $item['name'] ? : $item['thumb']['title'];
            $item['image'] = $item['thumb']['origin'] == 'internal'
                ? '<img alt="'.htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8').'" class="d-block" src="'. $item['thumb']['thumb_url'].'"/>'
                : $item['thumb']['thumb_html'];
            $item['description'] = $item['model'];
            $item['info_url'] = $item['href'] ? : $item['thumb']['main_url'];
            $item['buy_url'] = $item['add'];
            if (!$display_price) {
                $item['price'] = '';
            }
            $review = $button_write;
            if ($item['rating']) {
                $review = $item['rating'];
            }
        } else {
            $item['info_url'] = $item['href'];
            $item['image'] = $item['icon_url']
                ? '<img alt="'.htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8').'" class="d-block" src="'. $item['icon_url'].'"/>'
                : $item['icon_code'];
        }
?>
                <li class="col-md-1">
                <?php if ($item[ 'resource_code' ]) {
                        echo $item[ 'resource_code' ];
                    } else {?>
                    <a href="<?php echo $item['info_url'] ?>"><?php echo $item['image'] ?></a>
                    <a class="productname" href="<?php echo $item['info_url'] ?>"><?php echo $item['title']?></a>
                    <?php if ($review_status) { ?>
                    <span class="procategory"><?php echo $item['rating']?></span>
                    <?php } ?>
            <?php if($item['price']){?>
                   <span class="price">
                    <?php  if ($item['special']) { ?>
                           <div class="pricenew"><?php echo $item['special']?></div>
                           <div class="priceold"><?php echo $item['price']?></div>
                    <?php } else { ?>
                           <div class="oneprice"><?php echo $item['price']?></div>
                    <?php } ?>
                   </span>
            <?php } } ?>
                </li>

            <?php
            }
        }
        ?>
    </ul>

    <?php if ($block_framed) { ?>
    </div>
    <?php } ?>
</div>
