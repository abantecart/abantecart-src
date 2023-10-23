<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
    <h4 class="modal-title"><?php echo $modal_title ?></h4>
</div>

<div id="ct_form" class="tab-content">
    <?php echo $form['form_open']; ?>
    <div class="panel-body panel-body-nopadding">
        <?php
        foreach ($form['fields'] as $name => $field) {
            //Logic to calculate fields width
            $widthclasses = "col-sm-6";
            if (is_int(stripos($field->style, 'large-field'))) {
                $widthclasses = "col-sm-7";
            } else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
                $widthclasses = "col-sm-5";
            } else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
                $widthclasses = "col-sm-3";
            } else if (is_int(stripos($field->style, 'tiny-field'))) {
                $widthclasses = "col-sm-2";
            }
            $widthclasses .= " col-xs-12"; ?>
            <div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>" <?php echo ($name=='other_type' ? 'style="display: none;"' : '')?>>
                <label class="control-label col-sm-4 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
                <div class="input-group afield <?php echo $widthclasses; ?>">
                    <?php if ($name == 'seo_keyword') { ?>
                    <span class="input-group-btn">
                        <?php echo $keyword_button; ?>
                    </span>
                    <?php }
                    echo $field; ?>
                </div>
                <span id="error_<?php echo $name?>" class="help-block field_err"></span>
            </div>
        <?php } ?>
    </div>
    <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 center">
                    <button class="btn btn-primary on_save_close lock-on-click">
                        <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
                    </button>
                    <a class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-close"></i> <?php echo $form['cancel']->text; ?>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="application/javascript">
    $(document).ready(function () {
        $('#generate_seo_keyword').click(function () {
            let seo_name = $('#page_name').val().replace('%', '');
            $.get('<?php echo $generate_seo_url;?>&seo_name=' + seo_name, function (data) {
                $('#seo_keyword').val(data).change();
            });
        });
    });

    $('.on_save_close').on('click', function(e){
        e.preventDefault();
        $('span.help-block.field_err').html('').parents('.form-group').removeClass('has-error');
        $.ajax({
            url: '<?php echo $form['form_open']->action; ?>',
            type: 'POST',
            data: $('#pageLayoutFrm').serializeArray(),
            dataType: 'json',
            success: function (data) {
                if(data == null){
                    alert('Oops, something went wrong. Try to check error log.');
                }else {
                    location = '<?php echo $redirect_url;?>' + '&tmpl_id=' + $('select#tmpl_id').val() + '&page_id=' + data.page_id + '&layout_id=' + data.layout_id;
                }
            },
            error: function(xhr){
                for(let i in xhr.responseJSON.errors){
                    $('#error_'+i).html(xhr.responseJSON.errors[i]).parents('.form-group').addClass('has-error');
                }
            }
        });
        return false;
    });
</script>