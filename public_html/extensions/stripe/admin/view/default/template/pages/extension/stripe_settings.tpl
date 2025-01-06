<?php
include_once($tpl_common_dir.'action_confirm.tpl');
echo $extension_summary;
echo $tabs; ?>
<div id="content" class="panel panel-default">
    <div class="panel-heading col-xs-12">
        <div class="primary_content_actions pull-left">
            <?php
            if ($connected) { ?>
                <div class="btn-group mr10 toolbar">
                    <a class="btn btn-default" href="<?php echo $disconnect; ?>">
                        <i class="fa fa-chain-broken fa-fw"></i>
                        <?php echo $text_disconnect; ?>
                    </a>
                </div>
                <div class="btn-group mr10 toolbar">
                    <?php
                    if ($test_mode) { ?>
                        <a href="<?php echo $connect_url; ?>" class="stripe-connect" target="_new">
                            <span><?php echo $text_connect; ?> (live) </span>
                        </a>
                    <?php
                    } else { ?>
                        <a href="<?php echo $connect_url; ?>&mode=test" class="stripe-connect light-blue" target="_new">
                            <span><?php echo $text_connect; ?> (test) </span>
                        </a>
                    <?php
                    } ?>
                </div>
            <?php
            } else { ?>
                <div class="btn-group mr10 toolbar">
                    <a href="<?php echo $connect_url; ?>" class="stripe-connect" target="_new">
                        <span><?php echo $text_connect; ?> (live) </span>
                    </a>
                </div>
                <div class="btn-group mr10 toolbar">
                    <a href="<?php echo $connect_url; ?>&mode=test" class="stripe-connect light-blue" target="_new">
                        <span><?php echo $text_connect; ?> (test) </span>
                    </a>
                </div>
            <?php
            } ?>
        </div>
        <?php
        include_once($tpl_common_dir.'content_buttons.tpl'); ?>
    </div>
    <?php
    if (!$connected && !$skip_connect) { ?>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <label class="h4 heading"><?php echo $text_stripe_connect; ?></label>
            <div class="text-center mt10 mb10"></div>
        </div>
    <?php
    } else {
        echo $form['form_open']; ?>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12 <?php echo $test_mode ?'status_test' :''; ?>">
            <label class="h4 heading"><?php echo $text_stripe_settings; ?></label>
        <?php
            foreach ($form['fields'] as $name => $field) {
            //Logic to calculate fields width
            $widthCssClasses = "col-sm-7";
            if (str_contains($field->style, 'large-field')) {
                $widthCssClasses = "col-sm-7";
            } else {
                if (str_contains($field->style, 'medium-field') || str_contains($field->style, 'date')) {
                    $widthCssClasses = "col-sm-5";
                } else {
                    if (str_contains($field->style, 'small-field') || str_contains($field->style, 'btn_switch')) {
                        $widthCssClasses = "col-sm-3";
                    } else {
                        if (str_contains($field->style, 'tiny-field')) {
                            $widthCssClasses = "col-sm-2";
                        }
                    }
                }
            }
            $widthCssClasses .= " col-xs-12";
            if ($connected
                    && in_array(
                            $name,
                            [
                                    'stripe_test_mode',
                                    'stripe_pk_test',
                                    'stripe_pk_live',
                                    'stripe_sk_test',
                                    'stripe_sk_live'
                            ]
                )
            ) {
                continue;
            }
            ?>
            <div id="<?php echo $name; ?>" class="form-group <?php echo $error[$name] ? 'has-error' : ''; ?>">
                <label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>">
                    <?php echo ${$name}; ?>
                </label>
                <div class="input-group afield <?php echo $widthCssClasses; ?>">
                    <?php echo $field; ?>
                </div>
                <?php
                if ($error[$name]) { ?>
                    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                <?php
                } ?>
            </div>
            <?php
            } ?>
        </div>
        <div class="panel-footer col-xs-12">
            <div class="text-center">
                <button class="btn btn-primary">
                    <i class="fa fa-save fa-fw"></i>
                    <?php echo $form['submit']->text; ?>
                </button>
                <button class="btn btn-default" type="reset">
                    <i class="fa fa-refresh fa-fw"></i>
                    <?php echo $button_reset; ?>
                </button>
            </div>
        </div>
        </form>
    <?php
    } ?>
</div>
