
<section id="popularbrands">
    <div class="mt-5 container-fluid">
<?php if ( $block_framed ) { ?>
        <div class="block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>"
             id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>">
            <h2 class="ps-3 pe-2"><?php echo $heading_title; ?></h2>
            <h6 class="ps-3 pe-2"><?php echo $heading_subtitle; ?></h6>
<?php } ?>


    <div id="brandcarousal" class="d-flex flex-wrap overflow-hidden justify-content-center p-5  ">
    <?php
        foreach ($content as $item) { ?>
        <div class="card m-3" style="min-width: <?php echo $item['image']['width']+20;?>px !important;">
        <?php
        if ($item[ 'resource_code' ]) {
            echo $item[ 'resource_code' ];
        } else { ?>
            <a href="<?php echo $item['href']; ?>"
               title="<?php echo_html2view($item['name']);?>"
               class="btn btn-link d-block" >
                <img src="<?php echo $item['image']['thumb_url']; ?>"
                     class="card-img-top"
                     alt="<?php echo_html2view($item['name']);?>"
                     style="width: <?php echo $item['image']['width'];?>px; height: <?php echo $item['image']['height'];?>px; "
                >
            </a>
            <?php
            if($item['image']['title']){ ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $item['image']['title']; ?></h5>
                </div>
            <?php }
            } ?>
        </div>
        <?php
        }
    ?>
   </div>
<!--   <div class="clearfix"></div>-->
<!--   <a id="prev" class="prev" href="#">&lt;</a>-->
<!--   <a id="next" class="next" href="#">&gt;</a>-->
<!--   </div>-->
<?php if ( $block_framed ) { ?>
	</div>
<?php } ?>
</section>
