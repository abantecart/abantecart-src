<?php if ($error_warning) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li>
				<?php
				if (!empty($search_form)) {
					?>
					<form id="<?php echo $search_form['form_open']->name; ?>"
						  method="<?php echo $search_form['form_open']->method; ?>"
						  name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">

						<?php
						foreach ($search_form['fields'] as $f) {
							?>
							<div class="form-group">
								<div class="input-group input-group-sm">
									<?php echo $f; ?>
								</div>
							</div>
						<?php
						}
						?>
						<div class="form-group">
							<button type="submit"
									class="btn btn-xs btn-primary"><?php echo $search_form['submit']->text ?></button>
							<button type="reset" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i></button>
						</div>
					</form>
				<?php
				}
				?>
			</li>
			<li>
				<a class="itemopt" title="<?php echo $button_insert; ?>" href="<?php echo $insert_href; ?>" data-toggle="modal" data-target="#transaction_modal"><i class="fa fa-plus-circle"></i></a>
			</li>

			<?php if (!empty ($form_language_switch)) { ?>
				<li>
					<?php echo $form_language_switch; ?>
				</li>
			<?php } ?>
				<li>
					<div class="btn-group mr10 toolbar">
						<a class="btn btn-white disabled"><?php echo $balance; ?></a>
						<?php if($button_orders_count){ ?>
						<a target="_blank"
						   class="btn btn-white tooltips"
						   href="<?php echo $button_orders_count->href; ?>"
						   data-toggle="tooltip"
						   title="<?php echo $button_orders_count->title; ?>"
						   data-original-title="<?php echo $button_orders_count->title; ?>"><?php echo $button_orders_count->text; ?></a>
						<?php } ?>
						<a target="_blank"
						   class="btn btn-white tooltips"
						   href="<?php echo $actas->href; ?>"
						   data-toggle="tooltip"
						   title="<?php echo $actas->text; ?>"
						   data-original-title="<?php echo $actas->text; ?>"><i class="fa fa-male"></i></a>
					</div>

				</li>

			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
							<i class="fa fa-question-circle"></i>
						</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php echo $listing_grid; ?>
			</div>
		</div>
	</div>
</div>


<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'transaction_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'	));
?>

<script type="text/javascript">

	var updateViewButtons = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#transaction_modal');
		});
	};


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


	function ajaxReplace(data){
		var html = '';
		if(data.error!=undefined){
			$('#popup_text').before('<div class="warning alert alert-error alert-danger">'+data.error+'</div>');
		}
		if(data.fields){
			for(var f in data.fields){
				html += '<tr><td>'+ data.fields[f].text + '</td><td>' + data.fields[f].field + '</td></tr>';
			}
		}

		$('#popup_text').html(html);
        $("#popup_text input, #popup_text  select, #popup_text textarea").aform({triggerChanged: true, showButtons: false, autoHide:false });

		if( $('#transaction_form_transaction_type1').val()=='' && $('#transaction_form_transaction_type0').val()!='' ){
			$('#transaction_form_transaction_type1').val('').parents('tr').hide();
		}

	}


</script>
