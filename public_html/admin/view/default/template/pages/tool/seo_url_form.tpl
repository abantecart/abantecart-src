<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
    <div class="panel-heading col-xs-12">
        <div class="primary_content_actions pull-left">
            <div class="actionitem btn-group mr10 toolbar">
                <a class="btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>
        <?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
    </div>
    <?php echo $form['form_open']; ?>
    <div class="panel-body panel-body-nopadding tab-content col-xs-12">
        <div class="col-md-9 mb10">
            <?php foreach ($form['fields'] as $name => $field) { ?>
                <?php if ($name === 'language_id') { echo $field; continue; } ?>
                <div class="form-group <?php echo !empty($error[$name]) ? 'has-error' : ''; ?>">
                    <label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>">
                        <?php echo ${'entry_' . $name}; ?>
                    </label>
                    <div class="input-group afield col-sm-9 col-xs-12"><?php echo $field; ?></div>
                    <?php if (!empty($error[$name])) { ?>
                        <span class="help-block field_err col-sm-offset-3"><?php echo $error[$name]; ?></span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel-footer col-xs-12">
        <div class="text-center">
            <button class="btn btn-primary lock-on-click"><i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?></button>
            <button class="btn btn-default" type="reset"><i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?></button>
            <a class="btn btn-default" href="<?php echo $cancel; ?>"><i class="fa fa-arrow-left fa-fw"></i> <?php echo $button_cancel; ?></a>
        </div>
    </div>
    </form>
</div>
