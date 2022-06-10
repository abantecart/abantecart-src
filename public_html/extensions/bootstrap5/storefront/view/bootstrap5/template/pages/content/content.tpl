<h1 class="ms-3 my-2 heading-title ">
  <?php echo $heading_title; ?>
</h1>

<div class="container mt-5">
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
            <i class="fa fa-arrow-right"></i>
            <?php echo $button_continue->text ?>
        </a>
        <?php echo $this->getHookVar('post_content_button'); ?>
    </div>
</div>
