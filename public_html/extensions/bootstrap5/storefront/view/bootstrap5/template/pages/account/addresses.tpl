<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-address-book me-2"></i>
    <?php echo $heading_title; ?>
</h1>

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

<div class="container">
    <h4><?php echo $text_address_book; ?></h4>
    <?php echo $this->getHookvar('pre_address');?>
    <?php foreach ($addresses as $result) { ?>
    <div class="genericbox border-bottom">
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
    <?php } ?>
    <?php echo $this->getHookvar('post_address');?>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-evenly">
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

</div>
