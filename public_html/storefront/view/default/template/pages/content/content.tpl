<h1 class="ms-3 my-2 heading-title ">
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
    <?php } ?>
    <?php if ($children) { ?>
        <?php include( $this->templateResource('/template/pages/content/content_listing.tpl') ) ?>
    <?php } ?>

    <div class="d-flex flex-wrap m-3 justify-content-between align-items-center">
        <?php echo $this->getHookVar('pre_content_button'); ?>
        <?php echo $this->getHookVar('post_content_button'); ?>
    </div>

</div>
