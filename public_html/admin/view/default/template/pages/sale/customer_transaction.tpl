<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<ul class="nav nav-tabs nav-justified nav-profile">
	<?php
	foreach ($tabs as $tab) {
		if ($tab['active']) {
			$classname = 'active';
		} else {
			$classname = '';
		}
		?>
		<li class="<?php echo $classname; ?>">
			<a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
		</li>
	<?php } ?>

	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
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
			    <?php
                //for additional store show warning about login in that store's admin (because of crossdomain restriction)
                if($warning_actonbehalf){ ?>
                    data-confirmation="delete"
                    data-confirmation-text="<?php echo $warning_actonbehalf;?>"
                <?php } ?>
			       data-original-title="<?php echo $actas->text; ?>"><i class="fa fa-male"></i></a>
			</div>

			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" title="<?php echo $button_add; ?>" href="<?php echo $insert_href; ?>" data-toggle="modal" data-target="#transaction_modal">
				<i class="fa fa-plus"></i>
				</a>
			</div>
			<div class="btn-group mr10 toolbar">
			<?php if (!empty($search_form)) { ?>
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
			    		<button type="submit" class="btn btn-xs btn-primary tooltips" title="<?php echo $button_filter; ?>">
			    			<?php echo $search_form['submit']->text ?>
			    		</button>
			    		<button type="reset" class="btn btn-xs btn-default tooltips" title="<?php echo $button_reset; ?>">
			    			<i class="fa fa-refresh"></i>
			    		</button>

			    	</div>
			    </form>
			<?php } ?>
			</div>
				
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
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

</script>
