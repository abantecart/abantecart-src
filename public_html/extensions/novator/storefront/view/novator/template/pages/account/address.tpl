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
echo $form['form_open']; ?>
	<h4 class="mb-4 mt-3 pb-3 border-bottom"><?php echo $text_edit_address; ?></h4>
	<div class="card mb-4">
        <div class="card-body">
            <?php
                foreach ($form['fields'] as $fieldKey => $field) { ?>
                <div class="mb-3 row justify-content-md-center">
                    <label for="<?php echo $field->element_id?>" class="col-sm-12 col-md-5 col-form-label me-2">
                        <?php echo ${'entry_'.$fieldKey}; ?>
                    </label>
                    <div class="col-sm-12 col-md-6">
                        <?php echo $field; ?>
                        <span class="help-block text-danger"><?php echo ${'error_'.$fieldKey}; ?></span>
                    </div>
                </div>
            <?php } ?>
                <?php echo $this->getHookVar('address_edit_sections'); ?>
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
<script type="text/javascript">
    <?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $zone_id); ?>
    $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $('#AddressFrm_country_id').val());
</script>