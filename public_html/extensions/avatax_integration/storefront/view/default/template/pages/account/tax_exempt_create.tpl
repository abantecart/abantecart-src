<div class="ps-4 border p-3 mb-4">
    <h4><?php echo $text_tax_exemption; ?></h4>
        <?php
    foreach ($form['fields'] as $fieldKey => $field) {?>
        <div class="row mb-3">
            <label for="<?php echo $field->element_id ?>"
                   class=" col-sm-4 col-form-label me-2"><?php echo ${'entry_'.$fieldKey}; ?></label>
            <div class="col-sm-7 h-100">
                <?php
                echo $field; ?>
                <span class="help-block text-danger"><?php echo ${'error_' . $fieldKey}; ?></span>
            </div>
        </div>
        <?php
    }?>
</div>