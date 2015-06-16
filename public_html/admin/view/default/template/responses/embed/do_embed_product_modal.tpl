<div class="modal-header" xmlns="http://www.w3.org/1999/html">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_get_product_embed_code; ?></h4>
</div>
<div class="tab-content">
	<div class="panel-body panel-body-nopadding">
		<div class="col-sm-6 col-xs-12">
			<div id="embed_container" class="embed_preview"></div>
		</div>
		<div class="col-sm-6 col-xs-12">

			<label class="h4 heading"></label>
			<?php echo $form['form_open']; ?>
				<?php foreach ($fields as $field) {
				$widthclass = 'col-sm-6 col-xs-12';
				?>
				<div class="form-group">
					<?php if(${'entry_' . $field->name}){?>
					<label class="control-label col-sm-6 col-xs-12" for="<?php echo $field->element_id; ?>">
						<?php echo ${'entry_' . $field->name}; ?>
					</label>
					<?php }else{
						$widthclass = 'col-sm-12 col-xs-12';
					} ?>
					<div class="input-group afield <?php echo $widthclass; ?>">
						<?php echo $field; ?>
					</div>
				</div>
			<?php }  ?><!-- <div class="fieldset"> -->
			</form>

		</div>

		<div class="col-sm-12 col-xs-12">
			<div class="zero-clipboard"><span class="btn-clipboard">    </span></div>
			<div data-example-id="textarea-form-control" class="embed-code">
			    <form>
				    <?php echo $text_area;?>
			    </form>
			  </div>


		</div>
	</div>
</div>

<div id="code" style="display:none;"></div>

<script type="text/javascript"><!--
	// get multiline html to paste it then. NOTE: DO NOT USE MULTI-LINE COMMENT INSIDE *_code.tpl!!!
	var embed_base_code = function(){/*
<?php include(DIR_ROOT.'/'.RDIR_TEMPLATE.'template/responses/embed/product_code.tpl'); ?>
	*/}.toString().slice(14,-3).replace(/(\r\n|\n|\r)/gm,"");

	$('#getEmbedFrm_code_area').val(embed_base_code);
	$("#embed_container" ).html(embed_base_code );
	$("#code" ).html(embed_base_code );

	$(document).ready(function(){
		$('div#embed_modal').find('div.btn_switch').find('button').on('click', function(){
			var sorting = [".abantecart_image", ".abantecart_name", ".abantecart_price",".abantecart_qty",".abantecart_addtocart"];
			var switcher = $(this).parents('.input-group.afield').find('input');
			var classname = switcher.attr('name').replace('show_','abantecart_');
			var selector = '.'+classname;

			if(switcher.val()==1){
				var elm = $("#embed_container").find(selector);
				if(elm.length==0){
					//first of all get key
					var key = -1;
					for(var k in sorting){
						if(sorting[k] == selector){
							key = k;
							break;
						}
					}
					if(key==-1){ return false; }

					//get previous element for append
					k = key-1;
					var cn=''; // classname
					var t = -1; //target elm
					while(k>-1){
						cn = sorting[k];
						if($("#embed_container").find(cn)){
							t = k;
							break;
						}
						k--;
					}

					if(t >= 0) {
						$("#embed_container" ).find(sorting[t]).after('<div class="' + classname + '">&nbsp;</div>');
						$(embed_base_code).find(sorting[t]).after('<div class="' + classname + '">&nbsp;</div>')
					}
					//in case when no one previous elem there
					else{
						$("#embed_container" ).prepend('<div class="' + classname + '">&nbsp;</div>');
						$(embed_base_code).prepend('<div class="' + classname + '">&nbsp;</div>');
					}
				}
			}else{
				$("#embed_container" ).find(selector).remove();
				$(embed_base_code).find(selector).remove().end();

			}


			/*var d = new Date();
			var script_src = $("#embed_container" ).find('script').attr('src')+ '&time_stamp='+d.getTime();
			$("#embed_container" ).find('script').attr('src', script_src);*/
			var dd = $("#embed_container" ).html();
			$("#embed_container" ).html(dd);
			$('#getEmbedFrm_code_area').val(embed_base_code);

		});
	});



//--></script>


