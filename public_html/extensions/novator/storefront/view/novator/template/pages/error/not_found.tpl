<div class="row title">
    <div class="col-xl-12">
        <h2 class="h2 heading-title">
            <?php echo $heading_title; ?>
        </h2>
    </div>
</div>
<div class="container">
    <section class="mb-3">
        <h5 class="my-5"><?php echo $text_error; ?></h5>
        <?php
            if (empty($continue)){
                $continue = $button_continue->href;
            }
        ?>
        <a href="<?php echo $continue; ?>" class="btn btn-secondary me-2" title="<?php echo $button_continue->text ?>">
            <i class="bi bi-arrow-right"></i>
            <?php echo $button_continue->text ?>
        </a>
    </section>
</div>