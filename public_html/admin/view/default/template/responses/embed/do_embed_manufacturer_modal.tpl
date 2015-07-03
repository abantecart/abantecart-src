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
					<label class="control-label col-md-6 col-xs-12" for="<?php echo $field->element_id; ?>">
						<?php echo $label; ?>
					</label>
					<?php }else{
						$widthclass = 'col-sm-12 col-xs-12';
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
				<!--<span class="btn-clipboard btn-primary">Copy</span> -->
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
		'image': '<div class="abantecart_image"></div>\n',
		'name': '<div class="abantecart_name"></div>\n',
		'products_count': '<div class="abantecart_products_count"></div>\n'
	};

	var buildEmbedCode = function(){
		var html = '<script src="<?php echo $sf_js_embed_url; ?>" type="text/javascript"></script>\n';
			html += '<div style="display:none;" class="abantecart-widget-container" data-url="<?php echo $sf_base_url; ?>" data-css-url="<?php echo $sf_css_embed_url; ?>">\n';

		var d = new Date();
		$.each($('div#embed_modal').find("input[name='manufacturer_id[]']:checked, input[name='manufacturer_id[]'][type='hidden']"), function() {
		    id = $(this).val();
			html += '\t<div id="abc_' + (d.getTime() + id) + '" class="abantecart_manufacturer" data-manufacturer-id="'+ id +'">\n';

			$('#code_options').find('input[type="hidden"]').each(function(){
				if($(this).val()==1){
					html += '\t\t'+options[$(this).attr('name')];
				}
			});
			html += '\t</div>\n';
		});
		html += '</div>';
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


