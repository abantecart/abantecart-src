<?php if ($error_warning) { ?>
	<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div id="aPopup">
	<div class="popbox_tl" style="margin-top: 10px;">
		<div class="popbox_tr">
			<div class="popbox_tc"></div>
		</div>
	</div>
	<div class="popbox_cl"><div class="popbox_cr"><div class="popbox_cc" >
		<div class="aform">
			<div>
				<div class="tl"><div class="tr"><div class="tc"></div></div></div>
				<div class="cl"><div class="cr"><div class="cc">
					<div><?php echo $ajax_form_open?>
						<table id="popup_text" style="width: 100%"></table>
					</form>
					</div>
				</div></div></div>
				<div class="bl"><div class="br"><div class="bc"></div></div></div>
			</div>
		</div>
	</div></div></div>
	<div class="popbox_bl"><div class="popbox_br"><div class="popbox_bc"></div></div></div>
</div>

<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_customer"><?php echo $heading_title_transactions; ?></div>
				<?php
				if (!empty($search_form)) {
					echo '<div class="filter">';
					echo $search_form['form_open'];
					foreach ($search_form['fields'] as $f) echo $f;
					echo '<button type="submit" class="btn_standard">' . $search_form['submit'] . '</button>';
					echo '<button type="reset" class="btn_standard">' . $search_form['reset'] . '</button>';
					echo '</form>';
					echo '</div>';
				}
				?>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
						<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
										src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
					<div class="heading"><?php echo $balance; ?></div>
					<div class="buttons">
						<a style="vertical-align: top;" class="btn_toolbar" title="<?php echo $button_insert; ?>" ><span class="icon_add">&nbsp;</span></a>
						<?php echo $button_orders_count; ?>
						<?php echo $button_actas; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">
				<?php echo $listing_grid; ?>
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

	$('a[id^="action_view_"]').live('click', function () {
		var id = $(this).attr('id').replace('action_view_', '');
		show_popup(id);
		return false;
	});

	$(document).ready(function () {
		$(function () {
			var dates = $("#transactions_grid_search_date_start, #transactions_grid_search_date_end").datepicker({

				dateFormat: '<?php echo $js_date_format?>',
				changeMonth: false,
				numberOfMonths: 1,
				onSelect: function (selectedDate) {
					var option = this.id == "transactions_grid_search_date_start" ? "minDate" : "maxDate",
							instance = $(this).data("datepicker"),
							date = $.datepicker.parseDate(
									instance.settings.dateFormat ||
											$.datepicker._defaults.dateFormat,
									selectedDate, instance.settings);
					dates.not(this).datepicker("option", option, date);
				}
			});
		});
	});

	var $aPopup = $('#aPopup');

	function show_popup(id){
		var $aPopup = $('#aPopup').dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			width: 550,
			autoResize:true,
			title: (id>0 ? '<?php echo $popup_title_info;?>' : '<?php echo $popup_title_insert;?>'),
			open: function() {},
			resize: function(event, ui){
			},
			close: function(event, ui) {
				$(this).dialog('destroy');
			}
		});

		if(id>0){
			$('#aPopup').dialog({buttons:
										{"close": function(event, ui) {
												$(this).dialog('destroy');
											}}
								});
		}else{
			$('#aPopup').dialog({buttons:
										{"cancel": function(event, ui) {
												$(this).dialog('destroy');
										},
										"save": function(event, ui) {
											$('#transaction_form').submit();
										}}
								});
		}

		$aPopup.removeClass('popbox popbox2');

		$.ajax({
			url: '<?php echo $popup_action; ?>',
			type: 'GET',
			dataType: 'json',
			data: 'customer_transaction_id='+id,
			success: function(data) {
				if(data==null) return false;
				ajaxReplace(data);
				$aPopup.dialog('open');
			}
		});
	}

	function ajaxReplace(data){
		var html = '';
		if(data.error!=undefined){
			$('#popup_text').before('<div class="warning alert alert-error">'+data.error+'</div>');
		}
		if(data.fields){
			for(var f in data.fields){
				html += '<tr><td>'+ data.fields[f].text + '</td><td>' + data.fields[f].field + '</td></tr>';
			}
		}

		$('#popup_text').html(html);
        $("#popup_text input, #popup_text  select, #popup_text textarea").aform({triggerChanged: true, showButtons: false, autoHide:false });

		if( $('#transaction_form_transaction_type\\[1\\]').val()=='' && $('#transaction_form_transaction_type\\[0\\]').val()!='' ){
			$('#transaction_form_transaction_type\\[1\\]').val('').parents('tr').hide();
		}

	}

	$('#transaction_form_transaction_type\\[0\\]').live('change',function(){
		$('#transaction_form_transaction_type\\[1\\]').parents('tr').show();
	});

	$('#transaction_form').live('submit',function() {
			// submit the form
			var options = {
				dataType:'json',
				type: 'post',
				success:function (response) {
                    if(response.error!=undefined){
                        ajaxReplace(response);
                    }else{
                        location = location;
                    }

				}
			};
			$(this).ajaxSubmit(options);
			// return false to prevent normal browser submit and page navigation
			$(this).unbind('submit');
			return false;
		});
    $('.icon_add').click(function(){ show_popup(0); });
</script>
