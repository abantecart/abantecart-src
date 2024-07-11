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
                        <div class="row">
                            <div class="col-6 h-50 m-auto p-auto">
                                <h2 class="text-center"><?php echo html_entity_decode($res['title'])?></h2>
                                <h5 class="text-center"><?php echo html_entity_decode($res['description']);?></h5>
                            </div>
                            <div class="col-6 ms-auto">
                                <img src="<?php echo $res['main_url']?>" class="d-block ms-auto w-100 " alt="<?php echo_html2view($res['title'])?>">
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