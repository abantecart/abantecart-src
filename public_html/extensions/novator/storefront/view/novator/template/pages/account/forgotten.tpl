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
if ($error) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container">
    <?php
    $form[ 'form_open' ]->attr .= ' novalidate';
    $form[ 'form_open' ]->style .= ' needs-validation';
    echo $form[ 'form_open' ]; ?>
    <h5><?php echo $help_text; ?></h5>
    <div class="ps-4 border p-3 mb-4">
        <?php
        foreach ( $form['fields'] as $field_name => $field) { ?>
            <div class="mb-3 row  justify-content-md-center">
                <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-3 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                <div class="col-sm-5">
                    <?php echo $field; ?>
                </div>
            </div>
        <?php
        }
        echo $this->getHookVar('password_forgotten_sections'); ?>
    </div>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
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
</div>