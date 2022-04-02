<?php if($products){?>
<section id="<?php echo $homeBlockId;?>">
    <div class="container-fluid product-flex">
        <?php if ( $block_framed ) { ?>
        <div id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
             class="mt-5 block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>">
            <h2 class="ps-3 pe-2"><?php echo $heading_title; ?></h2>
            <h6 class="ps-3 pe-2"><?php echo $heading_subtitle; ?></h6>
        <?php }
            include($this->templateResource('/template/blocks/product_list.tpl'));
        if ( $block_framed ) { ?>
        </div>
    <?php } ?>
    </div>
</section>
<?php } ?>