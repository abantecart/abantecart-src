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
    <?php foreach ($addresses as $address) { ?>
    <div class="genericbox border-bottom">
        <table class="w-100">
            <tr>
                <td><address><?php echo $address['address']; ?></address></td>
                <td class="text-end">
                <?php
                    $address['button_edit']->style = ' btn btn-outline-primary';
                    $address['button_edit']->icon = ' fa fa-edit';

                    echo $address['button_edit'];
                    if ( !$address['default'] ) {
                        $address['button_delete']->style .= ' btn btn-outline-danger ';
                        $address['button_delete']->icon = ' fa fa-remove';
                        echo $address['button_delete'];
                    } ?>
                </td>
            </tr>
        </table>
    </div>
    <?php }
    echo $this->getHookvar('post_address');
    ?>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-evenly">
        <?php
        $button_back->style .= 'btn-secondary';
        $button_back->icon = 'fa fa-arrow-left';
        echo $button_back;
        echo $this->getHookvar('address_buttons');
        $button_insert->style .= ' btn-primary ';
        $button_insert->icon = 'fa fa-plus';
        echo $button_insert;
        ?>
    </div>
</div>
