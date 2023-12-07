<?php
if($resources){
?>
<section class="category-banner-sec p-0">
    <div class="container-fluid">
        <div id="categorySlides" class="carousel carousel-fade container">
            <div class="carousel-indicators">
                <?php foreach($resources as $k => $res){  ?>
                    <button type="button" data-bs-target="#categorySlides" data-bs-slide-to="<?php echo $k;?>" class="<?php echo $k == 0 ? 'active' : ''?> bg-secondary"></button>
                <?php } ?>
            </div>
            <div class="carousel-inner">
                <?php foreach($resources as $k => $res){?>
                    <div class="carousel-item <?php echo $k == 0 ? 'active' : ''?>">
                    <?php if($res['origin'] == 'internal'){ ?>
                        <div class="carousel-caption h-50">
                            <h2 class="my-auto"><?php echo html_entity_decode($res['title'])?></h2>
                            <h5><?php echo html_entity_decode($res['description']);?></h5>
                        </div>
                        <div class="col-12">
                            <img src="<?php echo $res['main_url']?>" class="d-block ms-auto" alt="<?php echo_html2view($res['title'])?>">
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