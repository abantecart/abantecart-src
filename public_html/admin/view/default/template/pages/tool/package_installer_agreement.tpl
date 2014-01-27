<?php if ($error) { ?>
<div class="warning alert alert-error"><?php echo $error; ?></div>
<?php } ?>
<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_extension"><?php echo $heading_title; ?></div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">
				<?php echo  $form['form_open']; ?>
				<div id="license" class="align_center">
					<div class="tl">
						<div class="tr">
							<div class="tc"></div>
						</div>
					</div>
					<div class="cl">
						<div class="cr">
							<div class="cc">
								<?php if ($incompability_form) { ?>
								<div class="attention" style=" margin-top: 200px;"><?php echo $version_incompatibility_text; ?></div>
								<?php } else if ($form['fuser']) {
									if($warning_ftp){ 	?>
								<div class="attention">
									<div style=" width: 500px; margin: auto; text-align: left; clear: both;"><?php echo $warning_ftp; ?></div>

									<div id="see_details_text" style=" display:none;width: 500px; margin: 20px auto auto auto ; text-align: left; clear: both;">
										<div class="cbox_tl">
											<div class="cbox_tr">
												<div style="min-height: 10px;" class="cbox_tc"></div>
											</div>
										</div>
										<div class="cbox_cl">
											<div class="cbox_cr">
												<div class="cbox_cc" style="max-height: 200px; overflow:auto;" >
													<?php echo $warning_ftp_details; ?>
												</div>
											</div>
										</div>
										<div class="cbox_bl">
											<div class="cbox_br">
												<div class="cbox_bc"></div>
											</div>
										</div>
									</div>

<script language="JavaScript" type="text/javascript">
	$('#see_details').click(function () {
		$('#see_details_text').slideToggle();
	});
</script>
								</div>
								<?php } ?>

									<div id="main_content">
										<div id="content_mid">
											<div class="contentBox loginBox">
												<div class="cbox_tl">
													<div class="cbox_tr">
														<div class="cbox_tc">
															<div class="heading icon_lockscreen"><?php echo $heading_title;?></div>
														</div>
													</div>
												</div>
												<div class="cbox_cl">
													<div class="cbox_cr">
														<div class="cbox_cc">
															<table style="width: 100%; text-align: left;">
																<tbody>
																<tr>
																	<td><?php echo $fhost;?></td>
																	<td><?php echo $form['fhost']; ?></td>
																</tr>
																<tr>
																	<td><?php echo $fuser;?></td>
																	<td><?php echo $form['fuser']; ?></td>
																</tr>
																<tr>
																	<td><?php echo $fpassword;?></td>
																	<td><?php echo $form['fpass']; ?></td>
																</tr>
																<tr>
																	<td></td>
																	<td align="right">
																		<button id="agree_button" class="btn_standard" type="submit"><?php echo $form['submit']; ?></button>
																	</td>
																</tr>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="cbox_bl">
													<div class="cbox_br">
														<div class="cbox_bc"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="clr_both"></div>
									</div>


								<?php } else { ?>
								<div class="license_text"><?php echo $license_text; ?></div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="bl">
						<div class="br">
							<div class="bc"></div>
						</div>
					</div>
					<div class="align_center" style="margin-top: 10px;">
						<?php
						if ($incompability_form) {
							?>
							<div style="margin-bottom: 200px;">
								<input type="hidden" name="disagree" id="disagree">
								<?php echo $form['agree']; ?>
								<button id="disagree_button" class="btn_standard" type="submit"
										onclick="$('#disagree').val('1');"><?php echo $form['disagree_button']; ?></button>
								<button id="agree_button" class="btn_standard"
										type="submit"><?php echo $form['submit']; ?></button>
							</div>
							<?php
						} else if (!$form['fuser']) {
							?>
							<input type="hidden" name="disagree" id="disagree">
								<?php echo $form['checkbox'] . '<label for="ftpFrm_agree">' . $text_agree;?></label>
							<br><br>
							<button id="disagree_button" class="btn_standard" type="submit"
									onclick="$('#disagree').val('1');"><?php echo $form['disagree_button']; ?></button>
							<button id="agree_button" class="btn_standard"
									type="submit"><?php echo $form['submit']; ?></button>
							<?php
						}  ?>
					</div>
					</form>
				</div>

			</div>
		</div>
		<div class="cbox_bl">
			<div class="cbox_br">
				<div class="cbox_bc"></div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#ftpFrm').submit(function () {
			if ($('#ftpFrm_agree').length > 0 && $('#disagree').val() != 1) {
				if (!$('#ftpFrm_agree').prop('checked')) {
					return false;
				}
			}
		});
	</script>
