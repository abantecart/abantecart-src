<?php if (!empty($error['warning'])) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="pull-right">
		    <div class="btn-group mr10">
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Archive"><i class="glyphicon glyphicon-hdd"></i></button>
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Report Spam"><i class="glyphicon glyphicon-exclamation-sign"></i></button>
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="glyphicon glyphicon-trash"></i></button>
		    </div>	
		    <div class="btn-group mr10">
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Archive"><i class="glyphicon glyphicon-hdd"></i></button>
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Report Spam"><i class="glyphicon glyphicon-exclamation-sign"></i></button>
		        <button class="btn btn-white tooltips" type="button" data-toggle="tooltip" title="" data-original-title="Delete"><i class="glyphicon glyphicon-trash"></i></button>
		    </div>	
		</div>
	
		<h5 class="subtitle mb5"><?php echo $form_title; ?></h5>
		<p class="text-muted">Showing 1 - 15 of 230 messages</p>
	</div>

	<div class="panel-body">
				
		<div class="table-responsive">
		
         <?php echo $summary_form; ?>
         <?php echo $form['form_open']; ?>
         <?php foreach ($form['fields'] as $section => $fields) { ?>
         <div class="fieldset">
		 	<div class="panel-heading">
            	<h3 class="heading"><?php echo ${'tab_' . $section}; ?></h3>
			</div>
			<div class="panel-body">
            	<table class="table form">
            	    <?php foreach ($fields as $name => $field) { ?>
            	    <tr>
            	        <td><?php echo ${'entry_' . $name}; ?></td>
            	        <td <?php echo ($name == 'description' ? 'class="ml_ckeditor"' : '')?> >
            	            <?php echo $field; ?>
            	            <?php if (is_array($error[$name]) && !empty($error[$name][$language_id])) { ?>
            	            <div class="field_err"><?php echo $error[$name][$language_id]; ?></div>
            	            <?php } else if (!empty($error[$name])) { ?>
            	            <div class="field_err"><?php echo $error[$name]; ?></div>
            	            <?php } ?>
            	        </td>
            	    </tr>
            	    <?php }  ?>
            	</table>
			</div>
         </div><!-- <div class="fieldset"> -->
         <?php }  ?>

         <div class="buttons align_center">
             <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
             <a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form['cancel']; ?></a>
         </div>
         </form>
		
		</div>
	
	</div>
</div>

<?php 
/* 
    <div class="cbox_tl">
        <div class="cbox_tr">
            <div class="cbox_tc">
                <?php echo $product_tabs ?>
                <div class="toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
                        src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
                    <?php endif; ?>
                    <?php echo $form_language_switch; ?>
					<div class="buttons">
						<a class="btn_toolbar" title="<?php echo $text_clone; ?>" href="<?php echo $clone_url; ?>">
							<span class="icon_grid_clone">&nbsp;</span>
						</a>
					</div>                    
                </div>
            </div>
        </div>
    </div>
*/
?>    


<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"><!--

$(document).ready(function () {
    var array = ['#productFrm_price',
        '#productFrm_cost',
        '#productFrm_shipping_price',
        '#productFrm_length',
        '#productFrm_width',
        '#productFrm_height',
        '#productFrm_weight'];

});

$('#productFrm_generate_seo_keyword').click(function(){
	var seo_name = $('#productFrm_product_description\\[name\\]').val().replace('%','');
	$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
		$('#productFrm_keyword').val(data).change();
	});
});

if (document.getElementById('productFrm_product_description[description]'))
    $('#productFrm_product_description\\[description\\]').parents('.afield').removeClass('mask2');
CKEDITOR.replace('productFrm_product_description[description]',
    {
        filebrowserBrowseUrl:false,
        filebrowserImageBrowseUrl:'<?php echo $rl; ?>',
        filebrowserWindowWidth:'920',
        filebrowserWindowHeight:'520',
        language:'<?php echo $language_code; ?>'
    }
);
//--></script>