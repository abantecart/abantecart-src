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
<?php }
$form['form_open']->style .= ' needs-validation';
$form['form_open']->attr .= ' novalidate';
echo $form['form_open'];
?>
    <h4 class="mb-3"><?php echo $text_your_details; ?></h4>
    <div class="card mb-4">
        <div class="card-body">
        <?php
            foreach ($form['fields'] as $field_name=>$field) { ?>
                    <div class="mb-3 row justify-content-md-center align-items-center">
                        <label for="<?php echo $field->element_id?>" class="col-sm-12 col-md-5 col-form-label me-2">
                            <?php echo ${'entry_'.$field_name}; ?>
                        </label>
                        <div class="col-sm-12 col-md-6">
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
        <?php
        $form['back']->style .= 'btn-secondary';
        $form['back']->icon = 'bi bi-arrow-left';
        echo $form['back'];

        $form['submit']->style .= ' btn-primary ms-auto lock-on-click';
        $form['submit']->icon = 'fa fa-check';
        echo $form['submit'];
        ?>
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