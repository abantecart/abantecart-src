
<div class="row title justify-content-center sec-heading-block text-center">
    <div class="col-xl-8">
        <h1 class="h2 heading-title mb-0 mt-lg-0 mt-3">
            <?php echo $heading_title; ?>
        </h1>
    </div>
</div>



    <h3 ><?php echo $description; ?></h3>
    <div class="row">
        <div class="col-md-12 pull-left">
            <?php echo $this->getHookVar('pre_content'); ?>
            <?php echo $content; ?>
            <?php echo $this->getHookVar('post_content'); ?>
        </div>
    </div>
    <div class="d-flex flex-wrap m-3 justify-content-between align-items-center">
        <?php echo $this->getHookVar('pre_content_button'); ?>
        <a href="<?php echo $continue; ?>" class="btn btn-secondary ms-auto mb-2" title="<?php echo_html2view($button_continue->text);?>">
            <i class="bi bi-arrow-right"></i>
            <?php echo $button_continue->text ?>
        </a>
        <?php echo $this->getHookVar('post_content_button'); ?>
    </div>

