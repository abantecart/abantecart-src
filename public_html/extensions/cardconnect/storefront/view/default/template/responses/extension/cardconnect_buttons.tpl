<?php if ($saved_cc_list) { ?>
<div class="saved_cards">
	<form id="cardconnect_saved_cc" class="validate-creditcard">
		<h4 class="heading4"><?php echo $text_saved_credit_card; ?></h4>

		<div class="form-group form-inline control-group">
			<span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
			<div class="col-sm-2 input-group controls">
				<a href="<?php echo $edit_address; ?>" class="btn btn-default btn-sm">
					<i class="fa fa-edit fa-fw"></i>
                    <?php echo $entry_edit; ?>
				</a>
			</div>
		</div>

		<div class="form-group form-inline control-group">
			<div class="col-sm-3 input-group">
				<select class="form-control input-medium short" id="use_saved_cc" name="use_saved_cc">
                    <?php
                    foreach ($saved_cc_list->options as $v => $option) {
                        echo "<option value='$v'>$option</option>";
                    }
                    ?>
				</select>
			</div>
			<div class="col-sm-1 input-group controls">
				<a id="delete_card" class="btn btn-default btn-sm"
				   title="<?php echo $text_delete_saved_credit_card; ?>">
					<i class="fa fa-trash-o fa-fw"></i>
                    <?php echo $text_delete_saved_credit_card; ?>
				</a>
			</div>
            <?php if ($save_cc) { ?>
				<div class="col-sm-1 input-group">
					<a id="new_card" class="btn btn-info btn-sm" title="<?php echo $text_new_credit_card; ?>">
						<i class="fa fa-plus fa-fw"></i>
                        <?php echo $text_new_credit_card; ?>
					</a>
				</div>
            <?php } ?>
		</div>

		<div class="form-group action-buttons text-center">
			<a id="<?php echo $back->name ?>" href="<?php echo $back->href; ?>" class="btn btn-default mr10">
				<i class="fa fa-arrow-left"></i>
                <?php echo $back->text ?>
			</a>
			<button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>"
					type="submit">
				<i class="fa fa-check"></i>
                <?php echo $submit->text; ?>
			</button>
		</div>

	</form>

</div>

<div class="enter_card" style="display:none;">
    <?php } else { ?>

	<div class="enter_card">
        <?php } ?>

		<form id="cardconnect" class="validate-creditcard">
			<h4 class="heading4"><?php echo $text_credit_card; ?></h4>

            <?php echo $this->getHookVar('payment_table_pre'); ?>

			<div class="form-group form-inline control-group">
				<span class="subtext"><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...</span>
				<div class="col-sm-2 input-group controls">
					<a href="<?php echo $edit_address; ?>" class="btn btn-default btn-sm">
						<i class="fa fa-edit fa-fw"></i>
                        <?php echo $entry_edit; ?>
					</a>
				</div>
			</div>

			<div class="form-group control-group">
				<label class="col-sm-4 control-label"><?php echo $entry_cc_owner; ?></label>
				<div class="col-sm-7 input-group controls">
					<input type="text" class="form-control " placeholder="Name on the card:"
						   value="<?php echo $cc_owner->value; ?>" id="cc_owner" name="cc_owner">
				</div>
				<span class="help-block"></span>
			</div>
			<div class="form-group form-inline control-group">
				<label class="col-sm-4 control-label"><?php echo $entry_cc_number; ?></label>
				<div class="col-sm-5 input-group controls">
                    <?php
                    $port = $this->config->get('cardconnect_test_mode') ? 6443 : 8443;
                    ?>
					<iframe id="tokenframe"
							name="tokenframe"
							src="https://<?php echo $api_domain;?>/itoke/ajax-tokenizer.html?invalidinputevent=true&css=<?php echo urlencode("input{border:1px solid rgb(204, 204, 204); width: 150px; padding: 6px 12px; height: 20px; font-size: 14px; line-height: 1.42857143; color: rgb(85, 85, 85); background-color: rgb(255, 255, 255); } body{ margin: 0;} .error{color: red;}");?>"
							frameborder="0" scrolling="no" width="100%"
							height="35"></iframe>
					<input type="hidden" name="cc_token" id="cc_token">
				</div>
                <?php if ($save_cc) { ?>
					<div class="input-group col-sm-2 ml10">
						<label>
							<a data-toggle="tooltip"
							   data-original-title="<?php echo $entry_cc_save_details; ?>"><?php echo $entry_cc_save; ?></a>
						</label>
						<input type="checkbox" value="0" id="save_cc" name="save_cc"
							   style="position: relative; margin-left: 0;">
					</div>
                <?php } ?>
				<span class="help-block"></span>
			</div>
			<div class="form-group form-inline control-group">
				<label class="col-sm-4 control-label"><?php echo $entry_cc_expire_date; ?></label>
				<div class="col-sm-3 input-group controls">
					<select data-placeholder="" class="form-control input-medium short" id="cc_expire_date_month"
							name="cc_expire_date_month">
                        <?php
                        foreach ($cc_expire_date_month->options as $v => $option) {
                            echo "<option value=\"".$v."\" ".($v==$cc_expire_date_month->value ? 'selected':'').">$option</option>";
                        }
                        ?>
					</select>
				</div>
				<div class="col-sm-2 input-group controls">
					<select data-placeholder="" class="form-control short" id="cc_expire_date_year"
							name="cc_expire_date_year">
                        <?php
                        foreach ($cc_expire_date_year->options as $v => $option) {
                            echo "<option value='$v'>$option</option>";
                        }
                        ?>
					</select>
				</div>
				<span class="help-block"></span>
			</div>
			<div class="form-group form-inline control-group">
				<label class="col-sm-6 control-label"><?php echo $entry_cc_cvv2; ?> <a
							onclick="openModalRemote('#ccModal', '<?php echo $cc_cvv2_help_url; ?>')"
							href="Javascript:void(0);"><?php echo $entry_cc_cvv2_short; ?></a></label>
				<div class="input-group col-sm-3 controls">
					<input type="text" autocomplete="off" class="form-control short" placeholder="" value=""
						   id="cc_cvv2" name="cc_cvv2">
				</div>
				<span class="help-block"></span>
			</div>

            <?php echo $this->getHookVar('payment_table_post'); ?>

			<div class="form-group action-buttons text-center">
				<button id="<?php echo $submit->name ?>" class="btn btn-orange" title="<?php echo $submit->text ?>"
						type="submit">
					<i class="fa fa-check"></i>
                    <?php echo $submit->text; ?>
				</button>
			</div>
		</form>

	</div>

	<!-- Modal -->
	<div id="ccModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ccModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3><?php echo $entry_what_cvv2; ?></h3>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript"><!--
		jQuery(document).ready(function () {
			window.addEventListener('message',
				function (event) {
					try {
						var token = JSON.parse(event.data);
						var mytoken = $('#cc_token');
						mytoken.val(token.message);
					} catch (e) {
					}
				},
				false);

			var submitSent = false;
			$('#new_card').click(function () {
				$('.saved_cards').remove();
				$('.enter_card').show();

			});

			$('#delete_card').click(function () {
				var $form = $('#cardconnect_saved_cc');
				confirmSubmit($form, '<?php echo $delete_card_url; ?>');
			});

			$('#enter_card').hover(function () {
				$(this).tooltip('show');
			});

			$('#save_cc').change(function () {
				if ($(this).is(':checked')) {
					$(this).val(1);
				} else {
					$(this).val(0);
				}
			});

			$('#cardconnect_saved_cc').submit(function (event) {
				event.preventDefault();
				var $form = $(this);
				confirmSubmit($form, '<?php echo $action; ?>');
			});

			//validate submit
			$('#cardconnect').submit(function (event) {
				event.preventDefault();
				if (submitSent !== true) {
					submitSent = true;
					var $form = $(this);
					if (!$.aCCValidator.validate($form) || $('#cc_token').val().length < 1) {
						submitSent = false;
						return false;
					} else {
						confirmSubmit($form, '<?php echo $action; ?>');
					}
				}
			});

			function confirmSubmit($form, url) {
				$.ajax({
					type: 'POST',
					url: url,
					data: $form.find(':input'),
					dataType: 'json',
					beforeSend: function () {
						$('.alert').remove();
						$form.find('.action-buttons').hide();
						$form.find('.action-buttons').before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo $text_wait; ?></div>');
					},
					success: function (data) {
						if (!data) {
							$('.wait').remove();
							$form.find('.action-buttons').show();
							$form.before('<div class="alert alert-danger"><i class="fa fa-bug fa-fw"></i> <?php echo $error_unknown; ?></div>');
							submitSent = false;
							//clear cvv if something wrong(for next try)
							$('#cc_cvv2').val('');
							$.aCCValidator.checkCVV($('#cc_cvv2'));
						} else {
							if (data.error) {
								$('.wait').remove();
								$form.find('.action-buttons').show();
								$form.before('<div class="alert alert-warning"><i class="fa fa-exclamation fa-fw"></i> ' + data.error + '</div>');
								submitSent = false;
								//clear cvv if something wrong(for next try)
								$('#cc_cvv2').val('');
								$.aCCValidator.checkCVV($('#cc_cvv2'));
							}
							if (data.success) {
								location = data.success;
							}
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$('.wait').remove();
						$form.find('.action-buttons').show();
						$form.before('<div class="alert alert-danger"><i class="fa fa-exclamation fa-fw"></i> ' + textStatus + ' ' + errorThrown + '</div>');
						submitSent = false;
						//clear cvv if something wrong(for next try)
						$('#cc_cvv2').val('');
						$.aCCValidator.checkCVV($('#cc_cvv2'));
					}
				});
			}

			function openModalRemote(id, url) {
				$(id).modal({remote: url});
			}
		});
		//--></script>
