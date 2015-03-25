<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $order_tabs ?>
<div class="tab-content">

	<div class="panel-heading">

		<div class="pull-right">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>"
				   data-toggle="tooltip"
				   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
					<i class="fa fa-file-text"></i>
				</a>
				<?php if (!empty ($help_url)) : ?>
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
					   title="" data-original-title="Help">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				<?php endif; ?>
			</div>

			<?php echo $form_language_switch; ?>
		</div>

	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">

		<label class="h4 heading"><?php echo $tab_files; ?></label>

		<?php
		if ($order_downloads) { ?>
			<?php
			foreach ($order_downloads as $product_id => $val) { ?>
				<div class="download-list"><?php echo $val['product_thumbnail']['thumb_html'] ?>
					<h3><?php echo $val['product_name'] ?></h3></div>
				<?php
				$downloads = (array)$val['downloads'];
				foreach ($downloads as $download) { ?>
					<table class="table">
						<thead>
						<tr>
							<th><?php echo $column_download; ?></th>
							<th><?php echo $column_file; ?></th>
							<th><?php echo $column_mask; ?></th>
							<th><?php echo $column_remaining; ?></th>
							<th><?php echo $column_expire_date; ?></th>
							<th><?php echo $column_status; ?></th>
						</tr>
						</thead>
						<tbody>
						<tr></tr>
						<tr <?php echo !$download['is_file'] ? 'class="warning alert alert-error alert-danger"' : '' ?>>
							<td class="left"><a href="<?php echo $download['href'] ?>"
												target="_blank"><?php echo $download['name']; ?></a>
								<?php
								if($download['attributes']){ ?>
									<dl class="dl-horizontal product-options-list-sm">
								<?php
								foreach ($download['attributes'] as $name => $value) { ?>
									<dt><small>- <?php echo $name; ?></small></dt><dd><small><?php echo (is_array($value) ? implode(' ', $value) : $value); ?></small></dd>
								<?php }?>
									</dl>
								<?php } ?>
							</td>
							<td class="left">
								<?php echo $download['resource']; ?>
							</td>
							<td class="left"><?php echo $download['mask']; ?></td>
							<td class="right"><div class="afield pull-left"><?php echo $download['remaining']; ?></div></td>
							<td class="right"><div class="afield pull-left"><?php echo $download['expire_date']; ?></div></td>
							<td class="right">
								<div class="afield pull-left">
								<?php
								if (is_array($download['status'])) {
									?>
									<div class="alert alert-warning">
										<?php echo implode('<br>', $download['status']); ?>
									</div>
								<?php
								} else {
									echo $download['status'];
								}
								?></div></td>
						</tr>
						<tr>
							<td colspan="6"><?php if ($download['download_history']) { ?>
									<div class="caption"><?php echo $order_download_history; ?></div>
									<div class="download-history col-sm-7">
										<table class="table table-striped table-condensed">
											<thead>
												<tr>
													<th><?php echo $text_time; ?></th>
													<th><?php echo $text_filename ?></th>
													<th><?php echo $text_mask ?></th>
													<th><?php echo $text_download_percent ?></th>
												</tr>
											</thead>
											<tbody>
											<tr></tr>
											<?php foreach ($download['download_history'] as $history) { ?>
												<tr>
													<td><?php echo $history['time']; ?></td>
													<td><?php echo $history['filename'] ?></td>
													<td><?php echo $history['mask'] ?></td>
													<td><?php echo $history['download_percent'] ?>%</td>
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
								<?php } ?>
							</td>
						</tr>
						</tbody>
					</table>
				<?php } ?>
				<div class="pull-right push-download col-sm-3 col-xs-12"><?php echo $val['push_download']; ?></div>
			<?php }
		} ?>

	</div>

	<div class="panel-footer">
		<div class="row center">
			<div class="col-sm-6 col-sm-offset-3">
				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
				</button>
				&nbsp;
				<a class="btn btn-default" href="<?php echo $cancel; ?>">
					<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
				</a>
			</div>
		</div>
	</div>

	</form>
</div><!-- <div class="tab-content"> -->


