<h1 class="ms-3 my-2 heading-title ">
    <?php if ($icon_url) { ?>
        <img src="<?php echo $icon_url ?>" alt="<?php echo_html2view($heading_title); ?>" class="img-fluid" style="max-width: 200px; max-height: 200px">
    <?php } else if ($icon_code) {
        echo $icon_code;
    } ?>
    <?php echo $heading_title; ?>
</h1>

<div class="container-fluid mt-4 ms-1">
    <?php if ($content_info['content_id']) { ?>
        <h3 ><?php echo $description; ?></h3>
        <div class="row">
            <div class="col-md-12 mb-4">
                <?php echo $this->getHookVar('pre_content'); ?>
                <?php echo $content; ?>
                <?php echo $this->getHookVar('post_content'); ?>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <ul class="list-inline mt-2 mb-0">
                <?php foreach ($content_info['tags'] as $tag => $tag_url) { ?>
                    <li class="list-inline-item">
                        <a class="text-decoration-none" href="<?php echo $tag_url ?>">
                            <i class="fa fa-tags fa-fw"></i>
                            <?php echo $tag ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <?php if ($contents || $mode == 'list') { ?>
        <?php include( $this->templateResource('/template/pages/content/content_listing.tpl') ) ?>
    <?php } ?>

</div>
