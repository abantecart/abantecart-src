<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>


<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_shipping"><?php echo $heading_title; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

      <div style="display: inline-block; width: 100%;">
        <div id="tabs" class="vtabs">
          <?php foreach ($sections as $section) { ?>
          <a tab="#tab_location_<?php echo $section['section_id']; ?>"><?php echo $section['name']; ?></a>
          <?php } ?>
        </div>
        <?php foreach ($sections as $section) { ?>
        <?php $form = $section['form']; ?>
        <div id="tab_location_<?php echo $section['section_id']; ?>" class="vtabs_page">
		<?php echo $form['form_open']; ?>
        
		<div class="fieldset">
		  <div class="heading"><?php echo $section['form_title']; ?></div>
		  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
		  <div class="cont_left"><div class="cont_right"><div class="cont_mid">
	        
		        <table class="form">
		        	<?php foreach ($form['fields'] as $name => $field) { ?>
		          <tr>
		            <td><?php echo ${'entry_' . $name}; ?></td>
		            <td><?php echo $field; ?></td>
		          </tr>
		          <?php } ?>
		          <tr>
		            <td colspan="2"><?php echo $section['note']; ?></td>
		          </tr>
		        </table>
				<div class="buttons align_center">
			  		<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
			  		<a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
		    	</div>
		    	
		  </div></div></div>
	      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
		</div><!-- <div class="fieldset"> -->

		</form>	    	
        </div>

        <?php } ?>
	  </div>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<script type="text/javascript"><!--
jQuery(function(){
	$.tabs('#tabs a');
});
//--></script>
