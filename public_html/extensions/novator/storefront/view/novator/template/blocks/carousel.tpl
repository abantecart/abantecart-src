<div class="container text-center my-3">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" role="listbox">
<?php
foreach ($listItems as $si => $subitem) {
    $schActive = $subitem['current'] ? 'active' : '';
    $resource = $subitem['resources'][0];
    if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
        $img = '<img class="img-fluid" src="resources/image/'.$resource['resource_path'].'" />';
    } elseif ($resource['resource_code']) {
        $img = $resource['resource_code'];
    }
?>
                <div class="carousel-item <?php if ($si == 0) { echo 'active'; } ?>">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-img">
								<a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>">
                                <?php echo $img; ?>
								</a>
                            </div>
                            <div class="card-img-overlay">
								<a class="<?php echo $schActive; ?>" href="<?php echo $subitem['href']; ?>">
                                    <?php echo $subitem['text'] ?: $subitem['title'] ?: $subitem['name']; ?>
								</a>
							</div>
                        </div>
                    </div>
                </div>
<?php
}
?>
            </div>
            <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>