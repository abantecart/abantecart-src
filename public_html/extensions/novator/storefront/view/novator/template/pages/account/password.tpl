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
<?php } ?>

<?php if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>


	<?php echo $form_open; ?>
	<h6 class="mb-3"><?php echo $text_password; ?></h6>
	<div class="card mb-md-4">
            <div class="card-body">
        <?php
            $field_list = [];
            array_push($field_list, 'current_password', 'password', 'confirm');
            foreach ($field_list as $field_name) {
                $field = $$field_name;
                ?>
            <div class="mb-3 row">
                <label for="<?php echo $field->element_id?>" class="col-md-2 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                <div class="col-md-9 h-100">
                    <?php echo $field; ?>
                    <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                </div>
            </div>
        <?php }
        echo $this->getHookVar('password_edit_sections'); ?>
   </div>
        </div>
    <div class="py-3 col-12 d-flex flex-wrap">
        <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo $button_back->text ?>">
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
