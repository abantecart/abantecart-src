<!-- style="width: <?php echo $item['image']['width'];?>px; height: <?php echo $item['image']['height'];?>px; " -->
<section id="popularbrands" class="brand-section">
    <div class="container">
        <div class="row title justify-content-center">
            <div class="col-12">
                <?php if ( $block_framed ) { ?>
                <div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>" id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
                    <h2 class="h4 text-center text-warning"><?php echo $heading_title; ?></h2>
                    <h6 class="mb-4"><?php echo $heading_subtitle; ?></h6>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="brand-carousel owl-carousel owl-theme">
                    <?php
                        foreach ($content as $item) { ?>
                            <div class="item">
                                <div class="brand-card" style="min-width: <?php echo $item['image']['width']+20;?>px !important;">
                            
                                    <?php
                                    if ($item[ 'resource_code' ]) {
                                        echo $item[ 'resource_code' ];
                                    } else { ?>
                                        <a href="<?php echo $item['href']; ?>" title="<?php echo_html2view($item['name']);?>">
                                            <img src="<?php echo $item['image']['thumb_url']; ?>" class="d-block w-100 h-auto" width="130" height="56" alt="<?php echo_html2view($item['name']);?>" >
                                        </a>
                                        <?php
                                            if($item['image']['title']){ ?>
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $item['image']['title']; ?></h5>
                                                </div>
                                        <?php }
                                        } ?>
                                </div>
                            </div>
                        <?php }
                    ?>
                </div>
            </div>
        </div>
   </div>
   <script>
   (function () {
      var owl = $('.brand-carousel');
      owl.owlCarousel({
        loop: true,
        margin: 24,
        nav: false,
        responsiveClass: true,
        autoplay: true,
        autoplayTimeout: 3000,
        responsive: {
          0: {
            items: 2
          },
          600: {
            items: 5
          },
          1000: {
            items: 8,
          }
        }
      });
    })();
   </script>
</section>
