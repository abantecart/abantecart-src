<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="tab-content">
    <?php echo $tabs; ?>

    <div class="panel panel-default">
        <div class="panel-heading col-xs-12">
            <div class="pull-left">
                <a class="btn btn-white tooltips back-to-grid"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
                    <i class="fa fa-arrow-left fa-lg"></i>
                </a>
            </div>
            <?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
        </div>

        <a id="form"></a>
        <?php echo $head_form['form_open']; ?>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <?php foreach ($head_form['fields'] as $name => $field) {
                //Logic to calculate fields width
                $widthcasses = "col-sm-7";
                if (is_int(stripos($field->style, 'large-field'))) {
                    $widthcasses = "col-sm-7";
                } else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
                    $widthcasses = "col-sm-5";
                } else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
                    $widthcasses = "col-sm-4";
                } else if (is_int(stripos($field->style, 'tiny-field'))) {
                    $widthcasses = "col-sm-2";
                }
                $widthcasses .= " col-xs-12";
                ?>
                <div class="form-group <?php echo $error[$name] ? "has-error" : ""; ?>">
                    <label class="control-label col-sm-3 col-xs-12"
                           for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
                    <div class="input-group afield <?php echo $widthcasses; ?> <?php echo $name == 'description' ? 'ml_ckeditor' : ''; ?>">
                        <?php echo $field; ?>
                    </div>
                    <?php if($error[$name]) { ?>
                        <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="panel-footer col-xs-12">
            <div class="text-center">
                <button type="submit" class="btn btn-primary lock-on-click mr10">
                    <i class="fa fa-save"></i> <?php echo $head_form['button_save']->text; ?>
                </button>
                <a id="reset_field" class="btn btn-default" href="<?php echo $head_form['button_reset']->href; ?>">
                    <i class="fa fa-refresh"></i> <?php echo $head_form['button_reset']->text; ?>
                </a>
            </div>
        </div>
        </form>
    </div>
</div>
