<div class="row title justify-content-center sec-heading-block text-center">
    <div class="col-xl-8">
        <h1 class="h2 heading-title mb-0 mt-lg-0 mt-3">
            <?php echo $heading_title; ?>
        </h1>
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