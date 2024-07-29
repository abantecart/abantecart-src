<?php
if($resources){
?>
<section class="category-banner-sec p-0 pt-4 pt-md-0">
    <div class="container-fluid">
        <div id="categorySlides" class="carousel carousel-fade">
            <div class="carousel-indicators">
                <?php foreach($resources as $k => $res){  ?>
                    <button type="button" data-bs-target="#categorySlides" data-bs-slide-to="<?php echo $k;?>" class="<?php echo $k == 0 ? 'active' : ''?> bg-secondary"></button>
                <?php } ?>
            </div>
            <div class="carousel-inner">
                <?php foreach($resources as $k => $res){?>
                    <div class="carousel-item <?php echo $k == 0 ? 'active' : ''?>">
                    <?php if($res['origin'] == 'internal'){ ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <div class="col-12 col-md-6 h-50 m-md-auto p-md-auto">
                                <h2 class="text-center"><?php echo html_entity_decode($res['title'])?></h2>
                                <h5 class="text-center"><?php echo html_entity_decode($res['description']);?></h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <img src="<?php echo $res['direct_url']?>" class="d-block w-sm-100 w-md-auto h-auto h-md-100 ms-auto me-auto me-md-0"  alt="<?php echo_html2view($res['title'])?>">
                            </div>
                        </div>
                        <?php
                    }else{
                        echo $res['main_html'];
                    } ?>
                    </div>
                <?php
                } ?>
            </div>
        </div>
    </div>
</section>
<?php } ?>