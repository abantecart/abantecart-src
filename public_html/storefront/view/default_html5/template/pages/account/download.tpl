<h1 class="heading1">
	<span class="maintext"><i class="icon-cloud-download"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"></span>
</h1>

<div class="container-fluid">

	<?php foreach ($downloads as $download) { ?>
		<div class="row-fluid mb20">
			<div style="width: 45%; float: left; margin-bottom: 2px;">
				<b><?php echo $text_order; ?></b> <?php echo $download['order_id']; ?></div>
			<div style="width: 45%; float: right; margin-bottom: 2px; text-align: right;">
				<b><?php echo $text_size; ?></b> <?php echo $download['size']; ?></div>
			<div class="content" style="clear: both;">
				<div style="padding: 5px;">
					<table width="100%">
						<tr>
							<td width="25%"><?php echo $text_name; ?> <?php echo $download['name']; ?></td>
							<td width="25%"><?php echo $text_remaining; ?> <?php echo $download['remaining']; ?></td>
							<td width="25%"><?php echo $download['expire_date'] ? $text_expire_date .'&nbsp;&nbsp;'. $download['expire_date'] : ''; ?></td>
							<td rowspan="2" style="vertical-align: top; text-align: right;">
								<?php echo $download['link']; ?>
							</td>
						</tr>
						<tr>
							<td colspan="3"><?php echo $text_date_added; ?> <?php echo $download['date_added']; ?></td>
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