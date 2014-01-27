<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $resources_scripts; ?>
<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_category"><?php echo $heading_title; ?></div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
        <?php echo $form_language_switch; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $form['form_open']; ?>
	<div class="fieldset">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

          <table class="form">
            <?php foreach ($form['fields'] as $name => $field) { ?>
				<tr>
					<td><?php echo ${'entry_'.$name}; ?></td>
					<td id="smenu_<?php echo $name; ?>">
						<?php
                        switch ( $name ) {
                            case 'item_icon':
                                echo $icon.$field;
                                break;
                            default:
                                echo $field;
                        }
                          ?>
						<?php if (!empty($error[$name])) { ?>
							<div class="field_err"><?php echo $error[$name]; ?></div>
						<?php } ?>
					</td>
				</tr>
			<?php } //foreach ($form['fields'] as $name => $field)  ?>
	          <tr>
					<td><?php echo $entry_link_category; ?></td>
					<td><div class="flt_left"><?php echo $categories; ?></div><div class="flt_left">&nbsp;&nbsp;<a id="link_category" class="btn_standard" href="#"><?php echo $button_link; ?></a></div></td>
				</tr>
				<tr>
					<td><?php echo $entry_link_page; ?></td>
					<td><div class="flt_left"><?php echo $pages; ?></div><div class="flt_left">&nbsp;&nbsp;<a id="link_page" class="btn_standard" href="#" ><?php echo $button_link; ?></a></div></td>
				</tr>
          </table>

  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div><!-- <div class="fieldset"> -->
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>


<script type="text/javascript"><!--
jQuery(function($){

	$('#link_category').click(function(){
        var c_id = $('#menu_categories').val();
        var c_name = $.trim( $('#menu_categories option:selected').text() );
        $('input[name="item_url"]').val('product/category&path='+c_id);
		return false;
    });

	$('#link_page').click(function(){
        var c_id = $('#menu_information').val();
        var c_name = $.trim( $('#menu_information option:selected').text() );
        $('input[name="item_url"]').val('content/content&content_id='+c_id);
		return false;
    });

	function preselect(){
		var val = $.trim( $('input[name="item_url"]').val());
		var id = val.replace('product/category&path=', '');
		if(id.length>20){
			id = val.replace('content/content&content_id=', '');
			if(id.length<20){
				$('#menu_information').val(id).change();
			}
		}else{
			$('#menu_categories').val(id).change();
		}
	}

    $('#item_icon').click(function(){
        selectDialog('image', $(this).attr('id'));
        return false;
    });

	$(document).ready(preselect);
	$('input[name="item_url"]').change(preselect);
});

//--></script>