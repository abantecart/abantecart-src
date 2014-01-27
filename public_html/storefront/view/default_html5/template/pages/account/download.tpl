<h1 class="heading1" style="border:none;">
	<span class="maintext"><i class="icon-cloud-download"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"></span>
</h1>

<div class="container-fluid">
	<?php foreach ($downloads as $download) { ?>
		<div class="row-fluid mb20" style="border-top: 1px solid #EFEFEF;">
			<div style="width: 45%; float: left; margin-bottom: 2px;">
				<b><?php echo $text_order; ?></b> <?php echo $download['order_id']; ?></div>
			<div style="width: 45%; float: right; margin-bottom: 2px; text-align: right;">
				<b><?php echo $text_size; ?></b> <?php echo $download['size']; ?></div>
			<div class="content" style="clear: both;">
				<div style="padding: 5px;">
					<table class="download-list">
						<tr>
							<td style="width: 40%"><div><?php echo $download['thumbnail']['thumb_html']; ?></div>
								<div><?php echo $text_name.' '.$download['name'];
									if($download['attributes']){
									?>
									<br><div class="download-list-attributes">
										<?php foreach($download['attributes'] as $name=>$value){
												echo '<small>- '.$name.': '. (is_array($value) ? implode(' ',$value) : $value) .'</small>';
										}?>
									</div>
									<?php } ?>
								<br><?php echo $text_date_added; ?> <?php echo $download['date_added']; ?></div>
							</td>
							<td style="width: 20%"><?php if($download['remaining']){ echo $text_remaining; ?> <?php echo $download['remaining']; }?></td>
							<td style="width: 20%"><?php if($download['expire_date']) { echo $text_expire_date; ?> <?php echo $download['expire_date'];} ?></td>
							<td rowspan="2" ><?php echo $download['link']; ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="pagination"><?php echo $pagination; ?></div>

	<div class="control-group" style="border-top: 1px solid #EFEFEF;">
		<div class="controls pull-right">
			<div class=" row-fluid mt20 mb20"> <?php echo $button_continue; ?></div>
		</div>
	</div>

</div>