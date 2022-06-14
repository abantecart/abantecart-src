<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-thumbs-up"></i>
    <?php echo $heading_title; ?>
</h1>
<div class="container">
    <section class="mb-3 py-5">
        <?php echo $text_message; ?>
        <a href="<?php echo $continue; ?>" class="btn btn-secondary mt-3 me-2" title="<?php echo $continue_button->text ?>">
            <i class="fa fa-arrow-right"></i>
            <?php echo $continue_button->text ?>
        </a>
    </section>
</div>