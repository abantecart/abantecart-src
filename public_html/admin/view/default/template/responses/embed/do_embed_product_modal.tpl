<div class="modal-header" xmlns="http://www.w3.org/1999/html">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_get_product_embed_code; ?></h4>
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
				?>
				<div class="form-group">
					<?php if(${'entry_' . $field->name}){?>
					<label class="control-label col-md-6 col-xs-12" for="<?php echo $field->element_id; ?>">
						<?php echo ${'entry_' . $field->name}; ?>
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
				<span class="btn-clipboard btn-primary">Copy</span>
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
		'price': '<div class="abantecart_price"></div>\n',
		'rating': '<div class="abantecart_rating"></div>\n',
		'blurb': '<div class="abantecart_blurb"></div>\n',
		'quantity': '<div class="abantecart_quantity"></div>\n',
		'addtocart': '<div class="abantecart_addtocart"></div>\n'
	};

	var buildEmbedCode = function(){
		var html = '<script src="<?php echo $sf_js_embed_url; ?>" type="text/javascript"></script>\n';
			html += '<div style="display:none;" class="abantecart-widget-container" data-url="<?php echo $sf_base_url; ?>" data-css-url="<?php echo $sf_css_embed_url; ?>">\n';
			html += '\t<div id="abc_<?php echo (int)(microtime()*1000);?>" class="abantecart_product" data-product-id="<?php echo $product_id; ?>">\n';

		$('#code_options').find('input[type="hidden"]').each(function(){
			if($(this).val()==1){
				html += '\t\t'+options[$(this).attr('name')];
			}
		});
		html += '\t</div>\n</div>';
		return html;
	}

	window.abc_count = 0;
	var ec = buildEmbedCode();
	$('#getEmbedFrm_code_area').val(ec);
	$("#embed_container" ).html(ec);


	$(document).ready(function(){
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

	});





//--></script>


