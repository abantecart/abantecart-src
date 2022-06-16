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
	<?php
    $form['form_open']->style .= ' needs-validation';
    $form['form_open']->attr .= ' novalidate';
    echo $form['form_open']; ?>
	<h4><?php echo $text_edit_address; ?></h4>
	<div class="ps-4 border p-3 mb-4">
    <?php
        foreach ($form['fields'] as $field_name => $field) { ?>
            <div class="mb-3 row">
                <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2">
                    <?php echo ${'entry_'.$field_name}; ?>
                </label>
                <div class="col-sm-9 h-100">
                    <?php echo $field; ?>
                    <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                </div>
            </div>
        <?php } ?>
            <div class="mb-3 row">
                <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2">
                    <?php echo $entry_default; ?>
                </label>
                <div class="col-sm-9 h-100">
                    <?php echo $form['default']; ?>
                </div>
            </div>
            <?php echo $this->getHookVar('address_edit_sections'); ?>
        </div>
        <div class="ps-4 p-3 col-12 d-flex flex-wrap">
            <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo $form['back']->text ?>">
                <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
                <?php echo $form['back']->text ?>
            </a>
            <button id="submit_button" type="submit"
                    role="button"
                    class="btn btn-primary ms-auto lock-on-click"
                    title="<?php echo_html2view($form['submit']->name); ?>">
                <i class="fa <?php echo $form['submit']->icon; ?>"></i>
                <?php echo $form['submit']->name ?>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">

<?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $zone_id); ?>
$('#AddressFrm_country_id').change(function() {
    $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
});
$('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $('#AddressFrm_country_id').val());

</script>