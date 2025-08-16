<div class="row title">
    <div class="col-xl-12">
        <h1 class="h2 heading-title">
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
<?php echo $this->getHookvar('pre_address');
foreach ((array)$addresses as $address) { ?>
    <div class="genericbox card p-3 mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="w-100">
                    <tr>
                        <td>
                            <address><?php echo $address['address']; ?></address>
                        </td>
                        <td class="text-end">
                        <?php
                            $address['button_edit']->style = ' btn btn-outline-primary mb-2 mb-md-0';
                            $address['button_edit']->icon = ' bi bi-pencil';
                            echo $address['button_edit'];
                            if ( !$address['default'] ) {
                                $address['button_delete']->style .= ' btn btn-outline-danger ';
                                $address['button_delete']->icon = ' bi bi-trash';
                                echo $address['button_delete'];
                            } ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php }
echo $this->getHookvar('post_address');
?>
<div class="py-3 col-12 d-flex flex-wrap justify-content-between">
    <?php
    $button_back->style .= 'btn-secondary';
    $button_back->icon = 'bi bi-arrow-left';
    echo $button_back;
    echo $this->getHookvar('address_buttons');
    $button_insert->style .= ' btn-primary ';
    $button_insert->icon = 'bi bi-plus';
    echo $button_insert;
    ?>
</div>