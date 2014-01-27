<div id="product_download_form" class="additionalRow clean">

	<div id="notify_<?php echo $download_id; ?>" class="success alert alert-success" style="display: none;"></div>
	<?php echo $form['form_open'] . $form['fields']['download_id']; ?>
	<div class="fieldset flt_left">
		<div class="heading"><?php echo $text_download_information; ?></div>
		<div class="top_left">
			<div class="top_right">
				<div class="top_mid"></div>
			</div>
		</div>
		<div class="cont_left">
			<div class="cont_right">
				<div class="cont_mid">

					<table class="table">
						<tr>
							<td style="width: 250px; height: 180px;">
								<?php echo $resources_scripts . $resource . $form['fields']['download_rl_path']?>
							</td>
							<td><?php echo $entry_file_status . '&nbsp;' . $form['fields']['status']; ?>
								<br><br>
								<?php echo $date_added ? $entry_date_added . '&nbsp;&nbsp;&nbsp;' . $date_added : ''; ?>
								<br>
								<?php echo $date_modified ? $entry_date_modified . '&nbsp;&nbsp;&nbsp;' . $date_modified : ''; ?>
							</td>
						<tr>
						<?php if($preview){ ?>
						<tr>
							<td><?php echo $text_path ?></td>
							<td><a href="<?php echo $preview['href']?>" target="_blank" title="<?php echo $text_preview; ?>"><?php echo $preview['path']; ?></a></td>
						</tr>
						<?php } ?>
						<tr>
							<td><?php echo $entry_name ?></td>
							<td><?php echo $form['fields']['name'] ?></td>
						</tr>
						<tr>
							<td><?php echo $entry_mask?></td>
							<td><?php echo $form['fields']['mask']?></td>
						</tr>
						<tr>
							<td><?php echo $entry_activate ?></td>
							<td><?php echo $form['fields']['activate'] . '&nbsp;' . $form['fields']['order_statuses']; ?></td>
						</tr>
						<tr class="max_downloads">
							<td><?php echo $entry_max_downloads ?></td>
							<td><?php echo $form['fields']['max_downloads'] ?></td>
						</tr>
						<tr class="expire_days">
							<td><?php echo $entry_expire_days ?></td>
							<td><?php echo $form['fields']['expire_days'] ?></td>
						</tr>
						<tr>
							<td><?php echo $entry_sort_order ?></td>
							<td><?php echo $form['fields']['sort_order'] ?></td>
						</tr>

					</table>

				</div>
			</div>
		</div>
		<div class="bottom_left">
			<div class="bottom_right">
				<div class="bottom_mid"></div>
			</div>
		</div>
	</div>


	<div class="fieldset flt_right">
		<div class="heading"><?php echo $text_download_attributes; ?></div>
		<div class="top_left">
			<div class="top_right">
				<div class="top_mid"></div>
			</div>
		</div>
		<div class="cont_left">
			<div class="cont_right">
				<div class="cont_mid">
					<?php
					if($attributes){
					foreach ($attributes as $id => $attribute) { ?>
						<div style="vertical-align: top; margin-top: 20px; width: 130px;"
							 class="flt_left"><?php echo ${'entry_attribute_' . $id} ?></div>
						<div style="vertical-align: top; margin-top: 20px;"
							 class="flt_left"><?php echo $attribute; ?></div>
						<div class="clr_both"></div>
					<?php }}else { ?>
						<div style="vertical-align: top; text-align: center"><?php echo $text_no_download_attributes_yet; ?></div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="bottom_left">
			<div class="bottom_right">
				<div class="bottom_mid"></div>
			</div>
		</div>
	</div>

	<div class="clr_both"></div>
	<div id="rl_<?php echo $download_id; ?>" class="add_resource" style="margin-top: 10px;"><?php echo $rl; ?>

	</div>
	<div class="buttons align_center">
		<?php echo $form['cancel']; ?>
		<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	</div>
	<script type="text/javascript"><!--
		$(document).ready(function () {
			$('#downloadFrm<?php echo $download_id; ?>_activate').change();
		});

		$('#downloadFrm<?php echo $download_id; ?>_activate').on('change', function () {
			if ($(this).val() != 'order_status') {
				$('#downloadFrm<?php echo $download_id; ?>_activate_order_status_id').parents('.select_element').hide().next('.required').hide();

				if($(this).val() == 'before_order'){
					$(this).parents('table').find('tr.max_downloads,tr.expire_days').hide();
				}else{
					$(this).parents('table').find('tr.max_downloads,tr.expire_days').show();
				}

			} else {
				$('#downloadFrm<?php echo $download_id; ?>_activate_order_status_id').parents('.select_element').show().next('.required').show();
				$(this).parents('table').find('tr.max_downloads,tr.expire_days').show();
			}
		});

		$('#<?php echo $form['form_open']->name ?>').on('submit', function () {
			$.post($(this).attr('action'),
					$(this).serialize(),
					function (json) {
						location = location.href+'&download_id=<?php echo $download_id ? $download_id."'" : "'+json.download_id"; ?>;
					}
			).fail(function (xhr, textStatus, errorThrown) {
						$('#notify_<?php echo $download_id; ?>').removeClass('success alert-success').addClass('warning alert-error');
						$('#notify_<?php echo $download_id; ?>').html(errorThrown).fadeIn(500);
					});
			return false;
		});

		//--></script>

	</form>
</div>
