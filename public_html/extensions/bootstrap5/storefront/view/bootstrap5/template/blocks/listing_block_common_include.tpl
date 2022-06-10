<?php
if( isset(current((array)$products)['product_id']) ){
    include($this->templateResource('/template/blocks/product_cell_grid.tpl'));
}else{ ?>
    <section id="<?php echo $homeBlockId;?>">
        <div class="container-fluid mt-3 ">
            <?php if ( $block_framed ) { ?>
                <h2 class="ps-3 pe-2"><?php echo $heading_title; ?></h2>
            <?php } ?>
<?php
// categories and brands
    if (isset(current((array)$content)['category_id'])
        || isset(current((array)$content)['manufacturer_id'])
    ){ ?>

            <ul class="list-unstyled d-flex flex-wrap justify-content-evenly m-5">
                <?php foreach ($content as $item){ ?>
                 <li class="card d-flex flex-wrap align-items-center shadow mb-4 me-4">
                    <a class="p-5" href="<?php echo $item['href']; ?>">
                        <?php echo $item['thumb']['thumb_html']; ?>
                    </a>
                     <?php if(isset($item['category_id'])){?>
                    <div class="mb-2">
                        <a class="fs-4 nav-link text-decoration-none text-dark "
                           href="<?php echo $item['href']; ?>"><?php echo $item['name']; ?></a>
                    </div>
                     <?php } ?>
                </li>
                <?php } ?>
            </ul>
<?php }
    //media
    elseif(count(current((array)$content)) == 1 && isset(current($content)['thumb']) ){ ?>
        <ul class="list-unstyled d-flex flex-wrap justify-content-evenly m-5">
            <?php foreach ($content as $item){ ?>
             <li class="card d-flex flex-wrap align-items-center shadow mb-4 me-4">
                    <?php echo $item['thumb']['thumb_html']; ?>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>

    </div>
<?php } ?>