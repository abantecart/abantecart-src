<?php echo $head; ?>
<h1 class="heading1">
	<span class="maintext"><i class="fa fa-cloud-download"></i> <?php echo $heading_title; ?></span>
	<span class="subtext"></span>
</h1>

<div class="contentpanel">

	<?php foreach ($downloads as $download) { ?>
		<div class="container-fluid mt20">
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
							<td rowspan="2" >
							<?php if($download['text']) { ?>
							<a class="btn btn-primary disabled">
	    				    <i class="fa fa-download"></i>
	    		    		<?php echo $download['text']; ?>
							</a>
							<?php } else { ?>
							<a href="<?php echo $download['button']->href; ?>" class="btn btn-primary">
	    				    <i class="fa fa-download"></i>
	    		    		<?php echo $download['button']->text; ?>
							</a>
							<?php } ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="container-fluid"><?php echo $pagination_bootstrap; ?></div>

	<div class="container-fluid">
		<div class="col-md-12 mt20">
			<a href="<?php echo $button_continue->href ?>" class="btn btn-default pull-right">
	    		    <i class="fa fa-arrow-right"></i>
	    		    <?php echo $button_continue->text ?>
			</a>
		</div>
	</div>

</div>
<?php echo $footer; ?>