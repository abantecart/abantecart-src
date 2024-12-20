<?php
if( isset(current((array)$products)['product_id']) ){
    include($this->templateResource('/template/blocks/product_cell_grid.tpl'));
}else{ ?>
    <section id="<?php echo $homeBlockId;?>">
        <div class="container-fluid mt-3 ">
            <?php if ( $block_framed ) { ?>
                <h1 class="h2 heading-title"><?php echo $heading_title; ?></h1>
            <?php } ?>
<?php
    //content pages
    if ($content && (isset(current((array)$content)['content_id'])) ){ ?>
        <div class="list-unstyled justify-content-evenly m-3">
            <?php foreach ($content as $item){
                $item['image'] = $item['icon_url']
                    ? '<img alt="'.html2view($item['title']).'" class="d-block" src="'. $item['icon_url'].'"/>'
                    : $item['icon_code'];
                ?>
                <div class="align-items-center shadow p-3 mb-3 me-3 row">
                    <?php if($item['image']){ ?>
                        <div class="col-sm-2">
                            <a href="<?php echo $item['href']; ?>">
                                <?php echo $item['image']; ?>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <a href="<?php echo $item['href']; ?>">
                                <p class="w-"><?php echo $item['title']; ?> dafdfdas  ds f ads f dsa f</p>
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center h-100 p-4">
                            <a href="<?php echo $item['href']; ?>">
                                <?php echo $item['title']; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
<?php
// categories and brands
    } else if ($content && (isset(current((array)$content)['category_id']) || isset(current((array)$content)['manufacturer_id']))
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
    elseif($content && count(current((array)$content)) == 1 && isset(current((array)$content)['thumb'])){ ?>
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