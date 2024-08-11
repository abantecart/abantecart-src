<?php if ($success) { ?>
    <h1 class="ms-3 my-2 heading-title ">
        <i class="fa fa-thumbs-up me-2"></i>
        <?php echo $text_subscribe_register; ?>
    </h1>

    <div class="container-fluid">
        <h5 class="pt-5 mx-5"><?php echo $success; ?></h5>
        <div class="ps-4 p-3 col-12 d-flex flex-wrap">
            <a class="btn btn-primary ms-auto"
               href="<?php echo $continue->href; ?>"
               title="<?php echo_html2view($continue->text); ?>">
                <i class="fa <?php echo $continue->icon ?> me-2"></i>
                <?php echo $continue->text ?>
            </a>
        </div>
    </div>

<?php }else{ ?>
    <h1 class="ms-3 my-2 heading-title ">
        <i class="fa fa-group me-2"></i>
        <?php echo $text_subscribe_register; ?>
    </h1>

<?php if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container">
    <?php
    $form['form_open']->style .= ' needs-validation';
    $form['form_open']->attr .= ' novalidate';
    echo $form['form_open']; ?>
    <p><?php echo $text_account_already; ?></p>
    <h4><?php echo $text_your_details; ?></h4>
    <div class="ps-4 border p-3 mb-4">
        <?php
        if($form){
            foreach ($form as $field_name => $field) { ?>
            <div class="mb-3 row">
                <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                <div class="col-sm-6 h-100">
                    <?php echo $field; ?>
                    <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                </div>
            </div>
        <?php }
        }?>
        <?php echo $this->getHookVar('subscriber_hookvar'); ?>
    </div>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
        <a href="<?php echo $create_account->href; ?>" class="btn btn-secondary" title="<?php echo $create_account->text ?>">
            <i class="<?php echo $create_account->icon; ?>"></i>
            <?php echo $create_account->text ?>
        </a>
        <button id="submit_button" type="submit"
                role="button"
                class="btn btn-primary ms-auto lock-on-click"
                title="<?php echo_html2view($continue->name); ?>">
            <i class="fa <?php echo $continue->icon ?>"></i>
            <?php echo $continue->name ?>
        </button>
    </div>
</form>
</div>

    <div id="privacyPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="privacyPolicyModalLabel"><?php echo $text_agree_href_text; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $text_close; ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>