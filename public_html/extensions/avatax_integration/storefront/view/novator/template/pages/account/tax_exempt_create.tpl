<div class="card mb-4">
    <div class="card-body">

<h5 class="border-bottom pb-3 mb-4"><?php echo $text_tax_exemption; ?></h5>
<?php
foreach ($form['fields'] as $fieldKey => $field) {
    if ($field->type == 'hidden') {
        echo $field;
        continue;
    } ?>
    <div class="row my-2">
        <label for="<?php echo $field->element_id ?>"
               class=" col-sm-3 col-form-label me-2"><?php echo ${'entry_' . $fieldKey}; ?></label>
        <div class="col-sm-8 h-100">
            <?php
            echo $field; ?>
            <span class="help-block text-danger"><?php echo ${'error_' . $fieldKey}; ?></span>
        </div>
    </div>
    <?php
} ?>
    </div>
</div>