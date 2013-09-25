<div class="contentBox">
  <div class="cbox_tl">
	  <div class="cbox_tr">
		  <div class="cbox_tc">
			  <div class="heading icon_title_extension"><?php echo $heading_title; ?></div>
		  </div>
	  </div>
  </div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
		<table class="list" style="height: 350px;">
			<tr>
				<td class="center ml_field" style="border: 0;" id="percents">
				<?php if ($loading) { ?>
				<div class="ui-progressbar-value" id="progressbar">
					<h3><?php echo $loading; ?></h3>
					<img src="<?php echo $template_dir; ?>image/progressbar.gif"/></td>
				</div>
			    <div class="ui-progressbar-value" id="retry" style="display:none; text-align: center; vertical-align: middle;">
				    <h3><?php echo $text_download_error; ?></h3>
					<?php echo $form[ 'form_open' ] . implode("",$form[ 'hidden' ]);?>
				    <input type="hidden" name="disagree" id="disagree" value="0">
					<button class="btn_standard" type="submit" onclick=" $('#disagree').val('1');"><?php echo $form['cancel']; ?></button>
					<button class="btn_standard" type="submit"><?php echo $form[ 'retry' ]; ?></button>
					</form>
			    </div>
				<?php } else { ?>
				<div class="ui-progressbar-value" id="progressbar">
					<h3><?php echo $pack_info; ?></h3>
					<?php echo $form[ 'form_open' ] . $form[ 'hidden' ];
					if($form[ 'cancel' ]['link']){
					?>
					<a class="btn_standard" href="<?php echo $form[ 'cancel' ]['link']; ?>"><?php echo $form[ 'cancel' ]['text']; ?></a>
					<?php } ?>
					<button class="btn_standard" type="submit"><?php echo $form[ 'submit' ]; ?></button>
					</form>
				</div>
				<?php } ?>
			</tr>
		</table>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<?php if ($loading) { ?>
	<script type="text/javascript">
		$(function() {
			$.ajax({  type: 'POST',
				url: '<?php echo $url; ?>&start=1',
				timeout: 240000,
				error: function() {
					$('#progressbar').hide();
					$('#retry').show();
				},
				success: function(data) {
					if (data == 100) {
						window.location = '<?php echo $redirect; ?>';
					}else{
						$('#progressbar').hide();
						$('#retry').show();
					}
				}
			});

		});

	</script>
<?php } ?>