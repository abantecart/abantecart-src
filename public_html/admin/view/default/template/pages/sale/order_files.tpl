<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
<div class="cbox_tl">
	<div class="cbox_tr">
		<div class="cbox_tc">
			<div class="heading icon_title_order"><?php echo $heading_title; ?></div>
			<div class="heading-tabs">
				<?php
				foreach ($tabs as $tab) {
					echo '<a href="' . $tab['href'] . '" ' . ($tab['active'] ? 'class="active"' : '') . '><span>' . $tab['text'] . '</span></a>';
				}
				?>
			</div>
			<div class="toolbar">
				<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
									src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
				<?php endif; ?>
				<div class="buttons">
					<a href="<?php echo $invoice ?>" class="btn_standard"
					   target="_invoice"><?php echo $button_invoice ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="cbox_cl">
	<div class="cbox_cr">
		<div class="cbox_cc">

			<?php echo $summary_form; ?>
			<?php echo $form['form_open']; ?>
			<div class="fieldset">
				<div class="heading"><?php echo $form_title; ?></div>
				<div class="top_left">
					<div class="top_right">
						<div class="top_mid"></div>
					</div>
				</div>
				<div class="cont_left">
					<div class="cont_right">
						<div class="cont_mid order_files">
							<?php
							if ($order_downloads) { echo  $resources_scripts; ?>
								<?php foreach ($order_downloads as $product_id => $val) { ?>
								<div class="download-list" ><?php echo $val['product_thumbnail']['thumb_html']?>
									<h3><?php echo $val['product_name']?></h3></div>

								<?php
								$downloads = (array)$val['downloads'];
								foreach ($downloads as $download) { ?>
								<table class="list download-list">
									<thead>
										<tr>
											<th class="left" style="width: 22%;"><?php echo $column_download; ?></th>
											<th class="left" style="width: 18%;"><?php echo $column_file; ?></th>
											<th class="left" style="width: 10%;"><?php echo $column_mask; ?></th>
											<th class="right" style="width: 12%;"><?php echo $column_remaining; ?></th>
											<th class="right" style="width: 22%;"><?php echo $column_expire_date; ?></th>
											<th class="right" style="width: 15%;"><?php echo $column_status; ?></th>
										</tr>
									</thead>
									<tbody>
										<tr></tr>
										<tr <?php echo !$download['is_file'] ? 'class="warning alert alert-error"' :''?>>
											<td class="left"><a href="<?php echo $download['href']?>" target="_blank"><?php echo $download['name']; ?></a>
											<?php if($download['attributes']){	?>
												<br><div class="download-list-attributes">
													<?php foreach($download['attributes'] as $name=>$value){
															echo '<small>- '.$name.': '.(is_array($value) ? implode(' ',$value) : $value).'</small>';
													}?>
												</div>
											<?php } ?>
											</td>
											<td class="left">
												<?php echo $download['resource']; ?>
											</td>
											<td class="left"><?php echo $download['mask']; ?></td>

											<td class="right"><?php echo $download['remaining']; ?></td>
											<td class="right"><?php echo $download['expire_date']; ?></td>
											<td class="right"><?php
												if(is_array($download['status'])){ ?>
												<div class="error">
												<?php echo implode('<br>',$download['status']);?>
												</div>
											<?php }else{
													echo $download['status'];
												}

												?></td>
										</tr>
										<tr><td colspan="6"><?php if($download['download_history']){ ?>
												<div class="caption"><?php echo $order_download_history;?></div>
												<div class="download-history">
												<table>
													<tr>
														<th>
														<?php echo $text_time;?>
														</th>
														<th>
														<?php echo $text_filename?>
														</th>
														<th>
														<?php echo $text_mask?>
														</th>
														<th>
														<?php echo $text_download_percent?>
														</th>
													</tr>
													<tr></tr>
													<?php foreach($download['download_history'] as $history){?>
													<tr>
														<td>
														<?php echo $history['time'];?>
														</td>
														<td>
														<?php echo $history['filename']?>
														</td>
														<td>
														<?php echo $history['mask']?>
														</td>
														<td>
														<?php echo $history['download_percent']?>%
														</td>
													</tr>
													<?php }?>
												</table></div>

											<?php } ?>
										</td></tr>
									</tbody>
								</table>
							<?php } ?>
								<a class="pull-right add">&nbsp;</a>
								<div class="pull-right push-download"><?php echo $val['push']; ?></div>
							<?php	} } ?>
						</div>
					</div>
				</div>
				<div class="bottom_left">
					<div class="bottom_right">
						<div class="bottom_mid"></div>
					</div>
				</div>
			</div>
			<!-- <div class="fieldset"> -->
			<div class="buttons align_center">
				<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
				<a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form['cancel']; ?></a>
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


<script type="text/javascript"><!--

	$(".order_files a.add").on('click', function () {
		$(this).hide();
		$(this).next().show();
		return false;
	});

--></script>