<h1 class="ms-3 my-2 heading-title ">
  <?php echo $heading_title; ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<?php if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

    <div class="container">
        <h4 ><?php echo $text_edit_address; ?></h4>
        <div class="row">
            <div class="col-md-6 d-flex flex-column">
            <b class="mb-2"><?php echo $text_address; ?></b><br />
                <?php echo $store; ?><br />
                <address><?php echo $address; ?></address>
            </div>
            <div class="col-md-6 d-flex flex-column">
                <?php if ($telephone) { ?>
                <b class="mb-2"><?php echo $text_telephone; ?></b><br />
                    <a href="tel:<?php echo $telephone; ?>"><?php echo $telephone; ?></a><br />
                <br />
                <?php }
                if ($fax) { ?>
                <b class="mb-2"><?php echo $text_fax; ?></b><br />
                <?php echo $fax;
                } ?>
            </div>
        </div>

        <div class="row mt-2">
            <?php echo $form_output; ?>
        </div>
    </div>
