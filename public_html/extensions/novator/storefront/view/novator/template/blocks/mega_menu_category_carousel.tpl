<?php $carouselId = $carouselId ?: 'MegaMenuCategoryCarousel' ?>

<div class="container text-center my-3">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="<?php echo $carouselId;?>" class="carousel carousel-dark" >
            <div class="carousel-inner d-flex justify-content-between" role="listbox">
<?php
foreach ($listItems as $si => $subitem) {
    $schActive = $subitem['current'] ? 'active' : '';
    $resource = $subitem['resources'][0];
    if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
        $img = '<img class="image-fluid " src="resources/image/'.$resource['resource_path'].'" />';
    } elseif ($resource['resource_code']) {
        $img = $resource['resource_code'];
    } ?>
                <div class="carousel-item">
                    <div class="card">
                        <a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>" target="<?php echo $subitem['settings']['target']; ?>">
                            <div class="img-wrapper">
                                <?php echo $img; ?>
                            </div>
                        </a>
                        <div class="card-body">
                            <a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>" target="<?php echo $subitem['settings']['target']; ?>">
                                <h6 class="category-title menu-img-caption">
                                <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
                                </h6>
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

<script type="application/javascript">
    $(document).ready(
        function(){
            let scrollPosition = 0;
            let inner = $("#<?php echo $carouselId;?> .carousel-inner");
            let items = $("#<?php echo $carouselId;?> .carousel-item");
            $("#<?php echo $carouselId;?> .carousel-control-next").on("click", function () {
                const carouselWidth = inner[0].scrollWidth;
                const cardWidth = items.width();
                if (scrollPosition < (carouselWidth - cardWidth * 5)) {
                    scrollPosition += cardWidth;
                }else{
                    scrollPosition = 0;
                }
                inner.animate(
                    {
                        scrollLeft: scrollPosition
                    },
                    600
                );
            });

            $("#<?php echo $carouselId;?> .carousel-control-prev").on("click", function () {
                const cardWidth = items.width();
                if (scrollPosition > 0) {
                    scrollPosition -= cardWidth;
                    inner.animate(
                        { scrollLeft: scrollPosition },
                        600
                    );
                }
            });

            let multipleCardCarousel = document.querySelector(
                "#<?php echo $carouselId;?>"
            );
            if (window.matchMedia("(min-width: 768px)").matches) {
                //rest of the code
                new bootstrap.Carousel(
                    multipleCardCarousel,
                    {
                        interval: false
                    }
                );
            } else {
                $(multipleCardCarousel).addClass("slide");
            }

            $('.mega-menu a.dropdown-toggle').on(
                'mouseover',
                function(){
                    const carouselWidth = inner[0].offsetWidth>0 ? inner[0].offsetWidth : inner[0].scrollWidth;
                    const cardWidth = items.width();
                    const ctrl = $("#<?php echo $carouselId;?>").find(".carousel-control-next,  .carousel-control-prev");
                    if(carouselWidth >= (cardWidth * items.length)){
                        ctrl.hide();
                    }else{
                        ctrl.show();
                    }
                }
            );
        }
    );
</script>