<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<?php foreach ($downloads as $download) { ?>
			<div style="display: inline-block; margin-bottom: 10px; width: 100%;">
				<div style="width: 45%; float: left; margin-bottom: 2px;">
					<b><?php echo $text_order; ?></b> <?php echo $download['order_id']; ?></div>
				<div style="width: 45%; float: right; margin-bottom: 2px; text-align: right;">
					<b><?php echo $text_size; ?></b> <?php echo $download['size']; ?></div>
				<div class="content" style="clear: both;">
					<div style="padding: 5px;">
						<table width="100%">
							<tr>
								<td><?php echo $download['thumbnail']['thumb_html']; ?></td>
								<td width="40%">
										<?php echo $text_name; ?> <?php echo $download['name']; ?>
										<?php if($download['attributes']){	?>
												<br><div>
													<?php foreach($download['attributes'] as $name=>$value){
															echo '- <small>'.$name.': '.(is_array($value) ? implode(' ',$value) : $value).'</small>';
													}?>
												</div>
										<?php } ?>
										<br><?php echo $text_date_added; ?> <?php echo $download['date_added']; ?>

								</td>
								<td width="20%"><?php if($download['remaining']){ echo $text_remaining; ?> <?php echo $download['remaining']; }?></td>
								<td width="20%"><?php if($download['expire_date']) { echo $text_expire_date; ?> <?php echo $download['expire_date'];} ?></td>
								<td width="11%" rowspan="2" style="text-align: right;"><?php echo $download['link']; ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
		<div class="buttons">
			<table>
				<tr>
					<td align="right"><?php echo $button_continue; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>