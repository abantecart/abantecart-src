<?php $carouselId = $carouselId ?: 'MegaMenuCategoryCarousel' ?>

<div class="container text-center my-3">
    <div class="row mx-auto my-auto  product-multi-carousel">
        <div id="<?php echo $carouselId;?>" class="carousel carousel-dark carousel-fade" >
            <div class="carousel-inner m-0 d-flex flex-nowrap justify-content-between row" role="listbox">
<?php
foreach ($listItems as $si => $subitem) {
                $schActive = $subitem['current'] ? 'active' : '';
                $resource = $subitem['resources'][0];
                if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                    $img = '<img class="image-fluid " src="resources/image/'.$resource['resource_path'].'" />';
                } elseif ($resource['resource_code']) {
                    $img = $resource['resource_code'];
                }else{
                    $img = '<img class="image-fluid " src="image/no_image.jpg">';
                } ?>
                <div class="carousel-item <?php echo !$si ? 'active' : '';?>">
                    <div class="card">
                        <a id="menu_<?php echo $subitem['item_id'];?>_img" class="<?php echo $schActive; ?>"
                           href="<?php echo $subitem['href']; ?>" target="<?php echo $subitem['settings']['target']; ?>">
                            <div class="img-wrapper"><?php echo $img; ?></div>
                        </a>
                        <div class="card-body">
                            <a id="menu_<?php echo $subitem;?>" class="<?php echo $schActive; ?>"
                               href="<?php echo $subitem['href']; ?>" target="<?php echo $subitem['settings']['target']; ?>">
                               <h6 class="category-title menu-img-caption"><?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?></h6>
                            </a>
                        </div>
                    </div>
                </div>
<?php } ?>
            </div>
            <?php if(count($listItems)>2){?>
            <a class="carousel-control-prev bg-transparent w-auto" href="#<?php echo $carouselId;?>" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next bg-transparent w-auto" href="#<?php echo $carouselId;?>" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
            <?php } ?>
        </div>
    </div>
</div>
