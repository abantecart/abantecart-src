<div class="row title">
    <div class="col-xl-12">
        <h2 class="h2 heading-title">
            <?php echo $heading_title; ?>
        </h2>
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

<div class="container">
    <?php
    $form_open->style .= ' needs-validation';
    $form_open->attr .= ' novalidate';
    echo $form_open; ?>
    <h5 class="mb-3"><?php echo $text_new_password; ?></h5>
    <div class="card mb-4">
            <div class="card-body">
        <?php
            $field_list = [];
            array_push($field_list, 'password', 'confirm');
            foreach ($field_list as $field_name) {
        ?>
            <div class="mb-3 row justify-content-md-center">
                <label for="<?php echo $$field_name->element_id?>" class="text-nowrap col-sm-3 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                <div class="col-sm-5 h-100">
                    <?php echo $$field_name; ?>
                    <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                </div>
            </div>
        <?php
            }
        ?>
        <?php echo $this->getHookVar('password_edit_sections'); ?>
   </div>
        </div>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
        <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo_html2view($button_back->text) ?>">
            <i class="<?php echo $button_back->icon; ?>"></i>
            <?php echo $button_back->text ?>
        </a>
        <button id="submit_button" type="submit"
                role="button"
                class="btn btn-primary ms-auto lock-on-click"
                title="<?php echo_html2view($submit->name); ?>">
            <i class="fa <?php echo $submit->icon; ?>"></i>
            <?php echo $submit->name ?>
        </button>
    </div>
    </form>
</div>