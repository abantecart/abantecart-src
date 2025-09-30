 <h4><?php echo $text_tax_exemption; ?></h4>
<div class="card mb-4">
    <div class="card-body">
    <?php
    if ($text_status) { ?>
        <div class="mb-3 row justify-content-md-center align-items-center">
            <label for="av_status_text" class="col-sm-12 col-md-5 col-form-label me-2">
                <?php echo $entry_status; ?>
            </label>
            <div id="av_status_text" class="col-sm-12 col-md-6"><?php echo $text_status; ?></div>
        </div>
    <?php }
        foreach ($form['fields'] as $field_name=>$field) {
            if($field->type == 'hidden') {
                echo $field;
                continue;
            }?>
                <div class="mb-3 row justify-content-md-center align-items-center">
                    <label for="<?php echo $field->element_id?>" class="col-sm-12 col-md-5 col-form-label me-2">
                        <?php echo ${'entry_'.$field_name}; ?>
                    </label>
                    <div class="col-sm-12 col-md-6">
                        <?php echo $field; ?>
                        <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                    </div>
                </div>
        <?php } ?>
    </div>
</div>