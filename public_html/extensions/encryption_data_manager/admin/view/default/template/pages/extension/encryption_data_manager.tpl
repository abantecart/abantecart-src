<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

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
	        
     		<?php if ($section['section_id'] == 'enc_usage') { ?>
     			<?php 
     				$unc_count = 0; 
     				foreach ($unencrypted_stats as $unc_stats) {
     					$unc_count += $unc_stats['count'];
     				}
     			?>
     			<?php if ($unc_count > 0) { ?>
     			<h4><?php echo $text_unencrepted_records ?></h4>
     			<table class="list" width="80%">
     			<tr>
     				<td width="300">
     				<ul>
     				<?php $unc_count = 0; 
     					foreach ($unencrypted_stats as $unc_stats) { ?>
     					<li><?php echo $unc_stats['table']; ?> : <?php echo $unc_stats['count']; ?> <?php echo $text_usage_records; ?></li>
     				<?php $unc_count += $unc_stats['count'];
     					 } ?>
     				</ul>
     				</td>
     				<td width="300">
	     			<?php echo $warn_encrypt_open_data; ?>
					</td>
				</tr>
				</table>	
				<?php } ?>
				<?php if (count ($section['usage_details']) > 0) { ?>
				<h4><?php echo $text_encrepted_records ?></h4>
				
     			<table class="list" width="80%">
     			<tr>
     				<th><?php echo $text_usage_heading_key_id; ?></th>
     				<th><?php echo $text_usage_heading_key_name; ?></th>
     				<th><?php echo $text_usage_heading_key_tables; ?></th>
     				<th><?php echo $text_usage_heading_key_rotate; ?></th>
     			</tr>
     			<?php foreach ($section['usage_details'] as $usage) { ?>
     			<tr>
     				<td width="60"><?php echo $usage['key_id']; ?></td>
     				<td width="120"><?php echo $usage['key_name']; ?></td>
     				<td width="400">
     				<ul>
     				<?php $enc_count = 0; 
     					foreach ($usage['key_usage'] as $enc_stats) { ?>
     					<li><?php echo $enc_stats['table']; ?> : <?php echo $enc_stats['count']; ?> <?php echo $text_usage_records; ?></li>
     				<?php $enc_count += $enc_stats['count'];
     					 } ?>
     				</ul>     				
					</td>
     				<td><?php if($enc_count > 0) { echo $usage['actons']; } ?></td>
     			</tr>
     			<?php } ?>
     			</table>
        		<?php } // endof enc_usage section ?>
        
     		<?php } else { ?>
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
     		<?php } ?>

				<div class="top10 buttons align_center">
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
	$.tabs('#tabs a');
});
//--></script>
