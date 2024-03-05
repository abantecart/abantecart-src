<div class="container text-center my-3">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="recipeCarousel" class="carousel carousel-dark" >
            <div class="carousel-inner d-flex justify-content-between" role="listbox">
<?php
foreach ($listItems as $si => $subitem) {
    $schActive = $subitem['current'] ? 'active' : '';
    $resource = $subitem['resources'][0];
    if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
        $img = '<img class="d-block h-auto" src="resources/image/'.$resource['resource_path'].'" />';
    } elseif ($resource['resource_code']) {
        $img = $resource['resource_code'];
    }
?>
                <div class="carousel-item">
                    <div class="card">
                        <a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>">
                            <div class="img-wrapper">
                                <?php echo $img; ?>
                            </div>
                        </a>
                        <div class="card-body">
                            <a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>">
                                <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
                            </a>
                        </div>
                    </div>
                </div>
<?php } ?>
            </div>
            <a class="carousel-control-prev bg-transparent w-auto" href="#recipeCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next bg-transparent w-auto" href="#recipeCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>

<script type="application/javascript">
    $(document).ready(
        function(){
            let scrollPosition = 0;
            $("#recipeCarousel .carousel-control-next").on("click", function () {
                const carouselWidth = $("#recipeCarousel .carousel-inner")[0].scrollWidth;
                const cardWidth = $("#recipeCarousel .carousel-item").width();
                if (scrollPosition < (carouselWidth - cardWidth * 5)) { //check if you can go any further
                    scrollPosition += cardWidth;  //update scroll position
                    $("#recipeCarousel .carousel-inner").animate(
                        {
                            scrollLeft: scrollPosition
                        },
                        600
                    );
                }
            });

            $("#recipeCarousel .carousel-control-prev").on("click", function () {
                const cardWidth = $("#recipeCarousel .carousel-item").width();
                if (scrollPosition > 0) {
                    scrollPosition -= cardWidth;
                    $("#recipeCarousel .carousel-inner").animate(
                        { scrollLeft: scrollPosition },
                        600
                    );
                }
            });

            let multipleCardCarousel = document.querySelector(
                "#recipeCarousel"
            );
            if (window.matchMedia("(min-width: 768px)").matches) {
                //rest of the code
                new bootstrap.Carousel(multipleCardCarousel, {
                    interval: false
                });
            } else {
                $(multipleCardCarousel).addClass("slide");
            }

            // function toggleCarouselControl(){
            //     const carouselWidth = $("#recipeCarousel .carousel-inner")[0].scrollWidth;
            //     const cardWidth = $("#recipeCarousel .carousel-item").width();
            //     const ctrl = $("#recipeCarousel").find(".carousel-control-next,  .carousel-control-prev");
            //     if(carouselWidth <= cardWidth * $("#recipeCarousel .carousel-item").length){
            //         ctrl.hide();
            //     }else{
            //         ctrl.show();
            //     }
            // }
        }
    );
</script>