<?php if ($error) { ?>
<div class="warning"><?php echo $error; ?></div>
<?php } ?>
<div class="contentBox">
  <div class="cbox_tl">
	   <div class="cbox_tr">
		   <div class="cbox_tc">
			   <div class="heading icon_title_extension"><?php echo $heading_title; ?></div>
            </div>
		</div>
  </div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	  <?php echo  $form['form_open']; ?>
		  <div id="license" class="align_center">
			<div class="tl"><div class="tr"><div class="tc"></div></div></div>
			<div class="cl"><div class="cr"><div class="cc">
<?php   if($form['fuser']){	?>

				<div class="warning"><?php echo $warning_ftp; ?></div>
<?php
if($form['fuser']){

?>          <table style="height: 350px; border:0; width: 100%">
					<tr>
						<td style="width:40%;"></td>
						<td style="width:60%;"></td>
					</tr>
			 <td class="align_right ml_field"><?php echo $fuser.'</td><td class="align_left ml_field">'.$form['fuser']; ?></td>
			</tr>
			<tr>
			  <td class="align_right ml_field"><?php echo $fpassword.'</td><td class="align_left ml_field">'.$form['fpass']; ?></td>
			</tr>
			<tr>
			  <td class="align_right ml_field"><?php echo $fhost.'</td><td class="align_left ml_field">'.$form['fhost']; ?></td>
			</tr>
			<tr>
			  <td class="align_right ml_field"><?php echo $fpath.'</td><td class="align_left ml_field">'.$form['fpath']; ?></td>
			</tr>
				<?php } ?>
			</table>

			<?php }else{ ?>
			  <div class="license_text"><?php echo $license_text; ?></div>
			<?php } ?>
			</div></div></div>
			<div class="bl"><div class="br"><div class="bc"></div></div></div>
		  <div class="align_center" style="margin-top: 10px;">
			  <?php
			  if(!$form['fuser']){	?>
				    <input type="hidden" name="disagree" id="disagree">
				    <?php echo $form['checkbox'].'<label for="ftpFrm_agree">'.$text_agree;?></label><br><br>
			        <button id="disagree_button" class="btn_standard" type="submit" onclick="$('#disagree').val('1');"><?php echo $form['disagree_button']; ?></button>
			        <button style="display:none;" id="agree_button" class="btn_standard" type="submit"><?php echo $form['submit']; ?></button>
			  <?php }else{ ?>
			 <button id="agree_button" class="btn_standard" type="submit"><?php echo $form['submit']; ?></button>
	          <?php }  ?>
		  </div>
		</form>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div