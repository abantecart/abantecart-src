<div class="modal-header" xmlns="http://www.w3.org/1999/html">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_get_manufacturer_embed_code; ?></h4>
</div>
<div class="tab-content do_embed">
    <div class="panel-body panel-body-nopadding">
        <div class="col-sm-6 col-xs-12">
            <div id="embed_container" class="embed_preview"></div>
        </div>
        <div id="code_options" class="col-sm-6 col-xs-12">
            <?php if (!empty ($help_url)) { ?>
				<div class="btn-group pull-right mr20">
				        <a class="btn btn-white tooltips"
				           href="<?php echo $help_url; ?>"
				           target="_ext_help"
				           data-toggle="tooltip"
				           title="<?php echo $text_external_help; ?>"
				           data-original-title="<?php echo $text_external_help; ?>">
				            <i class="fa fa-question-circle fa-lg"></i>
				        </a>
				</div>
		    <?php } ?>
			<label class="h4 heading"></label>
			<?php echo $form['form_open']; ?>
				<?php foreach ($fields as $field) {
				$widthclass = 'col-sm-6 col-xs-12';
				$label = ${'entry_' . str_replace(array('[', ']'), '', $field->name)};
				?>
				<div class="form-group">
					<?php if($label){?>
					<label class="control-label col-md-6 col-xs-6" for="<?php echo $field->element_id; ?>">
						<?php echo $label; ?>
					</label>
					<?php }else{
						$widthclass = 'col-sm-12 col-xs-6';
						if($field->name!='manufacturer_id[]'){
							$widthclass .= ' col-sm-offset-2 ';
						}
					} ?>
					<div class="input-group input-group-sm afield <?php echo $widthclass; ?>">
						<?php echo $field; ?>
					</div>
				</div>
			<?php }  ?>
			</form>

		</div>
        <div class="col-sm-12 col-xs-12">
            <div data-example-id="textarea-form-control" class="embed-code embed-url">
                <form>
                    <div class="input-group">
                        <?php echo $url; ?>
                        <span class="input-group-addon">
                            <span class="help_element">
                                <a id="copyEmbedUrlBtn" onclick="copyToClipboard('#getEmbedFrm_url', this); return false;" title="copy">
                                    <i class="fa fa-copy fa-lg"></i>
                                </a>
                            </span>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-12 col-xs-12">
            <div data-example-id="textarea-form-control" class="embed-code">
                <div class="btn-clipboard"><?php echo $text_copy_embed_code; ?>
                    <span class="help_element">
                        <a id="copyEmbedUrlBtn" onclick="copyToClipboard('#getEmbedFrm_code_area', this); return false;"
                           title="copy">
                            <i class="fa fa-copy fa-lg"></i>
                        </a>
                    </span>
                </div>
                <form>
                    <?php echo $text_area; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="code" style="display:none;"></div>

<script type="text/javascript">
	var options = {
		'image': '<span class="abantecart_image"></span>\n',
		'name': '<h3 class="abantecart_name"></h3>\n',
		'products_count': '<p class="abantecart_products_count"></p>\n'
	};

	var buildEmbedCode = function(){
        window.abc_count = 0;
		var common_params = '';
		var language = $('div#embed_modal').find('select[name="language"]').val();
		var currency = $('div#embed_modal').find('select[name="currency"]').val();
		if(language && language.length > 0){
			common_params += ' data-language="'+language+'"';
		}
		if(currency && currency.length > 0){
			common_params += ' data-currency="'+currency+'"';
		}
        let url = '<?php echo $direct_embed_url; ?>';
        url += '&lang=' + language;
        url += '&curr=' + currency;
        $('#code_options').find('input[type="hidden"]').each(function () {
            if ($(this).val() == 1) {
                url += '&' + $(this).attr('name') + '=1';
            }
        });

        var html = '<script src="<?php echo $sf_js_embed_url; ?>" type="text/javascript"><\/script>\n';
        html += '<ul style="display:none;" class="abantecart-widget-container" data-url="<?php echo $sf_base_url; ?>" data-css-url="<?php echo $sf_css_embed_url; ?>"' + common_params + '>\n';

        var d = new Date();
        $.each($('div#embed_modal').find("input[name='manufacturer_id[]']:checked, input[name='manufacturer_id[]'][type='hidden']"), function () {
            var id = $(this).val();
            html += '\t<li id="abc_' + (d.getTime() + id) + '" class="abantecart_manufacturer" data-manufacturer-id="' + id + '" data-language="' + language + '" data-currency="' + currency + '">\n';
            url += '&manufacturer_id[]=' + id;
            $('#code_options').find('input[type="hidden"]').each(function () {
                if ($(this).val() == 1) {
                    html += '\t\t' + options[$(this).attr('name')];
                }
            });
            html += '\t<\/li>\n';
        });
        html += '<\/ul>';
        $('#getEmbedFrm_code_area').val(html);

        //display preview
        $("#embed_container").html(html).find('div, a, button').css('pointer-events', 'none');
        $('#getEmbedFrm_url').val(url);
        recalcHeightParam = true;
    }

    recalcHeightParam = true;
    var calcHeight = function () {
        if (recalcHeightParam !== true) {
            return;
        }
        var outerHeight = 0;
        $('#embed_container.embed_preview')
            .find('.abantecart-widget-container')
            .children().each(
            function () {
                outerHeight += $(this).outerHeight();
            }
        );
        $('#getEmbedFrm_url').val($('#getEmbedFrm_url').val() + '&height=' + (outerHeight + 20));
        recalcHeightParam = false;
    };

    $(document).ready(function () {
        $('.do_embed a').tooltip();
        buildEmbedCode();

        $('div#embed_modal').find("input[name='manufacturer_id[]']").on('click', buildEmbedCode);
        $('div#embed_modal').find('div.btn_switch').find('button').on('click', buildEmbedCode);
        $('div#embed_modal').find('div.input-group').find('select').on('change', buildEmbedCode);

        preselect = function(){
          calcHeight();
          let $this = $(this);
          $this.select();
      }
      $("#getEmbedFrm_code_area, #getEmbedFrm_url").focus(preselect).click(preselect);
    });
</script>