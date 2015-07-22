<div class="modal-header" xmlns="http://www.w3.org/1999/html">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_get_manufacturer_embed_code; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding table-responsive">
		<div class="col-sm-6 col-xs-12">
			<div id="embed_container" class="embed_preview"></div>
		</div>
		<div id="code_options" class="col-sm-6 col-xs-12">

			<label class="h4 heading"></label>
			<?php echo $form['form_open']; ?>
				<?php foreach ($fields as $field) {
				$widthclass = 'col-sm-6 col-xs-12';
				$label = ${'entry_' . str_replace(array('[', ']'), '', $field->name)};		?>
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
			<?php }  ?><!-- <div class="fieldset"> -->
			</form>

		</div>

		<div class="col-sm-12 col-xs-12">

			<div data-example-id="textarea-form-control" class="embed-code">
				<div class="btn-clipboard"><?php echo $text_copy_embed_code; ?></div>
			    <form>
				    <?php echo $text_area;?>
			    </form>
			  </div>


		</div>
	</div>
</div>

<div id="code" style="display:none;"></div>

<script type="text/javascript"><!--
	var options = {
		'image': '<span class="abantecart_image"></span>\n',
		'name': '<h3 class="abantecart_name"></h3>\n',
		'products_count': '<p class="abantecart_products_count"></p>\n'
	};

	var buildEmbedCode = function(){
		var common_params = '';
		var language = $('div#embed_modal').find('select[name="language"]').val();
		var currency = $('div#embed_modal').find('select[name="currency"]').val();
		if(language && language.length > 0){
			common_params += ' data-language="'+language+'"';
		}
		if(currency && currency.length > 0){
			common_params += ' data-currency="'+currency+'"';
		}

		var html = '<script src="<?php echo $sf_js_embed_url; ?>" type="text/javascript"></script>\n';
			html += '<ul style="display:none;" class="abantecart-widget-container" data-url="<?php echo $sf_base_url; ?>" data-css-url="<?php echo $sf_css_embed_url; ?>"'+common_params+'>\n';

		var d = new Date();
		$.each($('div#embed_modal').find("input[name='manufacturer_id[]']:checked, input[name='manufacturer_id[]'][type='hidden']"), function() {
		    id = $(this).val();
			html += '\t<li id="abc_' + (d.getTime() + id) + '" class="abantecart_manufacturer" data-manufacturer-id="'+ id +'">\n';

			$('#code_options').find('input[type="hidden"]').each(function(){
				if($(this).val()==1){
					html += '\t\t'+options[$(this).attr('name')];
				}
			});
			html += '\t</li>\n';
		});
		html += '</ul>';
		return html;
	}

	window.abc_count = 0;
	var ec = buildEmbedCode();
	$('#getEmbedFrm_code_area').val(ec);
	$("#embed_container" ).html(ec);


	$(document).ready(function(){

		$('div#embed_modal').find("input[name='manufacturer_id[]']").on('click', function(){
			var ec = buildEmbedCode();
			window.abc_count = 0;
			$('#getEmbedFrm_code_area').val(ec);
			$("#embed_container" ).html(ec);
		});

		$('div#embed_modal').find('div.btn_switch').find('button').on('click', function(){
			var ec = buildEmbedCode();
			window.abc_count = 0;
			$('#getEmbedFrm_code_area').val(ec);
			$("#embed_container" ).html(ec);
		});

		$('div#embed_modal').find('div.input-group').find('select').on('change', function(){
			var ec = buildEmbedCode();
			window.abc_count = 0;
			$('#getEmbedFrm_code_area').val(ec);
			$("#embed_container" ).html(ec);
		});

		$(".btn-clipboard").click(function(){
			var txt = $('#getEmbedFrm_code_area').val();
			prompt ("Copy html-code, then click OK.", txt);
        });

		$("#getEmbedFrm_code_area").focus(function() {
		    var $this = $(this);
		    $this.select();

		    // Work around Chrome's little problem
		    $this.mouseup(function() {
		        // Prevent further mouseup intervention
		        $this.unbind("mouseup");
		        return false;
		    });
		});
	});
//--></script>