<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-face-anguished"></i>
    <?php echo $heading_title; ?>
</h1>
<div class="container">
    <section class="mb-3">
        <h5 class="my-5"><?php echo $text_error; ?></h5>
        <?php
            if (empty($continue)){
                $continue = $button_continue->href;
            }
        ?>
        <a href="<?php echo $continue; ?>" class="btn btn-secondary me-2" title="<?php echo $button_continue->text ?>">
            <i class="fa fa-arrow-right"></i>
            <?php echo $button_continue->text ?>
        </a>
    </section>
</div>