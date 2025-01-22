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


    <?php echo $form['form_open'];?>
        <h4 class="mb-3"><?php echo $text_your_details; ?></h4>
        <div class="card mb-4">
            <div class="card-body">
            <?php
                foreach ($form['fields'] as $field_name=>$field) { ?>
                        <div class="mb-3 row justify-content-md-center align-items-center">
                            <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                            <div class="col-sm-5">
                                <?php echo $field; ?>
                                <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                            </div>
                        </div>
                <?php }
                    echo $this->getHookVar('customer_attributes'); 
            ?>
            </div>
        </div>

        <div class="py-3 col-12 d-flex flex-wrap">
            <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo_html2view($form['back']->text); ?>">
                <i class="<?php echo $form['back']->icon; ?>"></i>
                <?php echo $form['back']->text ?>
            </a>
            <button id="submit_button" type="submit"
                    role="button"
                    class="btn btn-primary ms-auto lock-on-click"
                    title="<?php echo_html2view($form['continue']->name); ?>">
                <i class="bi bi-check"></i>
                <?php echo $form['continue']->name ?>
            </button>
        </div>
    </form>

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