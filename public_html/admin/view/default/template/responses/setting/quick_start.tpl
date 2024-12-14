<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php  echo $title; ?></h4>
</div>
<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
        <div class="panel-body panel-body-nopadding">
            <div class="form-group text-center">
                <label><?php echo $quick_start_note; ?></label>
            </div>
            <?php if(!$competed) { ?>
                <?php foreach ($form['fields'] as $name => $field) {
                    //Logic to calculate fields width
                    $widthcasses = "col-sm-7";
                    if ( is_int(stripos($field->style, 'large-field')) ) {
                        $widthcasses = "col-sm-7";
                    } else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
                        $widthcasses = "col-sm-5";
                    } else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
                        $widthcasses = "col-sm-3";
                    } else if ( is_int(stripos($field->style, 'tiny-field')) ) {
                        $widthcasses = "col-sm-2";
                    }
                    $widthcasses .= " col-xs-12"; ?>
                    <div class="form-group <?php if($error[$name]) { echo "has-error"; } ?>">
                        <label class="control-label col-sm-4 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
                        <div class="input-group afield <?php echo $widthcasses; ?> <?php echo (is_int(strpos($name, 'description')) ? 'ml_ckeditor' : '')?>">
                            <?php echo $field; ?>
                        </div>
                        <?php if ($error[$name]) { ?>
                        <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                        <?php } ?>
                    </div>
                    <?php
                    if($name=='template'){ ?>
                        <div class="form-group">
                            <label class="control-label col-sm-7 col-xs-12" ></label>
                            <div id="template_preview" class="input-group afield <?php echo $widthcasses; ?>">

                            </div>
                        </div>
                    <?php }
                    }
                } else {
                    echo $payments_selection;
                    echo $shipping_selection;
                    echo $language_selection;
                    echo $more_extensions; ?>
                    <br/>
                    <br/>
                    <div class="form-group text-center">
                        <label><?php echo $quick_start_last_footer; ?></label>
                    </div>
            <?php } ?>
        </div>
        <div class="panel-footer">
            <div class="row">
               <div class="center">
                <?php if($help_url){ ?>
                <div class="btn-group">
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help"
                        data-toggle="tooltip" data-original-title="<?php echo_html2view($text_external_help); ?>">
                        <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                </div>
                <?php }
                if ($back) { ?>
                <div class="btn-group">
                    <a class="btn btn-white step_back" href="<?php echo $back; ?>">
                        <i class="fa fa-arrow-left"></i> <?php echo $button_back; ?>
                    </a>
                </div>
                <?php }
                if ($competed) { ?>
                <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close fa-fw"></i> <?php echo $button_close; ?>
                </button>
                <?php } else { ?>
                <button class="btn btn-default" type="reset">
                    <i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
                </button>&nbsp;
                <button class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php echo $text_next; ?> <i class="fa fa-arrow-right"></i>
                </button>
                <?php } ?>
               </div>
            </div>
        </div>
	</form>
</div>
<?php
/** @see public_html/admin/view/default/template/responses/setting/quick_start_js.tpl */
require_once(RDIR_TEMPLATE.'/template/responses/setting/quick_start_js.tpl'); ?>