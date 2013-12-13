<tr id="<?php echo $file_id; ?>" class="optionRow">
	<td><?php echo $file_id; ?></td>
	<td><?php echo $icon; ?></td>
	<td><?php echo $form['fields']['name']->value; ?></td>
	<td><?php echo $form['fields']['max_downloads']->value; ?></td>
	<td><?php echo $form['fields']['sort_order']->value; ?></td>
	<td><?php echo $form['fields']['status']; ?></td>
	<td></td>
	<td><a id="<?php echo $file_id; ?>" href="#" class="expandRow"><?php echo $text_expand ?></a></td>
</tr>
<tr>
	<td colspan="8">
		<div class="additionalRow" style="display:none;">
			<?php echo $form['form_open']; ?>
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
										<?php
										//echo $thumbnail;
										//todo сделать запуск RL по клику на картинке типа как выбор лого сайта в сеттингах. пока вставил шо-нибудь
										?>
										<img src="<?php echo $thumbnail;?>" />
									</td>
									<td><?php echo $entry_file_status. '&nbsp;'. $form['fields']['status'] . $download_id;?>
										<br>
										<br>
										<?php echo $entry_date_added.'&nbsp;&nbsp;&nbsp;'.$date_added;?>
										<br>
										<br>
										<?php echo $entry_date_modified.'&nbsp;&nbsp;&nbsp;'.$date_modified;?>
									</td>
								<tr>
								<tr>
									<td><?php echo $entry_file_name?></td>
									<td><?php echo $form['fields']['name']?></td>
								<tr>
								<tr>
									<td><?php echo $entry_max_downloads?></td>
									<td><?php echo $form['fields']['max_downloads']?></td>
								<tr>
								<tr>
									<td><?php echo $entry_activate?></td>
									<td><?php echo $form['fields']['activate'].'&nbsp;'.$form['fields']['order_statuses']; ?></td>
								<tr>
								<tr>
									<td><?php echo $entry_expire_days?></td>
									<td><?php echo $form['fields']['expire_days'] ?></td>
								<tr>
								<tr>
									<td><?php echo $entry_shared?></td>
									<td><?php echo $form['fields']['shared'] ?></td>
								<tr>

								<tr>
									<td><?php echo $entry_sort_order?></td>
									<td><?php echo $form['fields']['sort_order'] ?></td>
								<tr>

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


			<div class="fieldset flt_left">
				<div class="heading"><?php echo $text_download_attributes; ?></div>
				<div class="top_left">
					<div class="top_right">
						<div class="top_mid"></div>
					</div>
				</div>
				<div class="cont_left">
					<div class="cont_right">
						<div class="cont_mid">
							<?php foreach ($attributes as $id => $attribute) { ?>
								<div style="vertical-align: top; margin-top: 20px; width: 130px;"
									 class="flt_left"><?php echo ${'entry_attribute_' . $id} ?></div>
								<div style="vertical-align: top; margin-top: 20px;"
									 class="flt_left"><?php echo $attribute; ?></div>
								<div class="clr_both"></div>
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
			<div id="rl_<?php echo $file_id; ?>" class="add_resource" style="margin-top: 10px;"><?php echo $rl; ?>

			</div>
			<div class="buttons align_center">
				<?php echo $form['cancel']; ?>
				<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
			</div>
			<script type="text/javascript"><!--
				$(document).ready(function(){
					$('#fileFrm<?php echo $file_id; ?>_activate').change();
				});

				$('#fileFrm<?php echo $file_id; ?>_activate').on('change',function(){
					if($(this).val()!='order_status'){
						$('#fileFrm<?php echo $file_id; ?>_order_status').parents('.select_element').hide().next('.required').hide();
					}else{
						$('#fileFrm<?php echo $file_id; ?>_order_status').parents('.select_element').show().next('.required').show();
					}
				});
			//--></script>
			</form>
		</div>
	</td>
</tr>