<h1 class="ms-3 my-2 heading-title ">
    <?php echo $heading_title; ?>
</h1>
<?php if ($content_info && $content_info['content_bar']) { ?>
<h6 class="m-2 p-2 text-secondary bg-light d-flex justify-content-between">
    <div>
        <?php if ($content_info['author']) { ?>
        <?php echo $text_author ?> <?php echo $content_info['author']; ?>
        <?php } ?>
    </div>
    <div><?php echo $text_published ?> <?php echo $publish_date; ?></div>
</h6>
<?php } ?>
<div class="container-fluid mt-4 ms-1">
    <?php if ($content_info['content_id']) { ?>
        <div class="d-flex flex-nowrap">
            <div>
                <?php if ($icon_url) { ?>
                    <img src="<?php echo $icon_url ?>" alt="<?php echo_html2view($heading_title); ?>" class="img-fluid">
                <?php } else if ($icon_code) {
                    echo $icon_code;
                } ?>
            </div>
            <?php if ($description) { ?>
            <div>
                <h3 ><?php echo $description; ?></h3>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-md-12 py-3">
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
