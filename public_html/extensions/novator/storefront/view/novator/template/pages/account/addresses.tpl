<div class="row title justify-content-center sec-heading-block text-center">
    <div class="col-xl-8">
        <h1 class="h2 heading-title mb-0 mt-lg-0 mt-3">
            <?php echo $heading_title; ?>
        </h1>
    </div>
</div>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }
if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>


    <h4 class="mb-4 mt-3 pb-3 border-bottom"><?php echo $text_address_book; ?></h4>
    <?php echo $this->getHookvar('pre_address');?>
    <?php foreach ($addresses as $result) { ?>
    <div class="genericbox card">
        <div class="card-body">
        <div class="table-responsive">
        <table class="w-100">
            <tr>
                <td><address><?php echo $result['address']; ?></address></td>
                <td class="text-end">
                <?php
                    $result['button_edit']->style = ' btn btn-outline-primary';
                    echo $result['button_edit'];
                    if ( !$result['default'] ) {
                        $result['button_delete']->style .= ' btn btn-outline-danger ';
                        echo $result['button_delete'];
                    } ?>
                </td>
            </tr>
        </table>
        </div>
        </div>
    </div>
    <?php } ?>
    <?php echo $this->getHookvar('post_address');?>

    <div class="py-3 col-12 d-flex flex-wrap justify-content-between">
        <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo_html2view($button_back->text); ?>">
            <i class="<?php echo $button_back->{'icon'}; ?>"></i>
            <?php echo $button_back->text; ?>
        </a>
        <?php echo $this->getHookvar('address_buttons');?>
        <a href="<?php echo $insert; ?>" class="btn btn-primary" title="<?php echo $button_insert->text ?>">
            <i class="<?php echo $button_insert->icon; ?>"></i>
            <?php echo $button_insert->text ?>
        </a>
    </div>


