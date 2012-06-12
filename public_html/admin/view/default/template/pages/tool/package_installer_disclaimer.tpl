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
			  <div class="license_text"><?php echo $text_disclaimer; ?></div>
			</div></div></div>
			<div class="bl"><div class="br"><div class="bc"></div></div></div>
		  </div>
		  <div class="align_center" style="margin-top: 10px;">
			 <input type="hidden" name="disagree" id="disagree" value="0">
			 <input type="hidden" name="disclaimer" id="disclaimer" value="1">
			 <?php echo $form['extension_key']; ?>
			 <button class="btn_standard" type="submit" onclick="$('#disagree').val('1');"><?php echo $form['disagree_button']; ?></button>
			 <button id="agree_button" class="btn_standard" type="submit"><?php echo $form['submit']; ?></button>
		  </div>
		</form>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div