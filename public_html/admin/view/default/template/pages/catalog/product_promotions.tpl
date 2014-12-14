<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>
<?php echo $product_tabs ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div class="panel-heading">
			<h4 class="panel-title"><?php echo $tab_discount; ?></h4>
		</div>
		<?php echo $form['form_open']; ?>
		<div class="panel-body panel-body-nopadding">
			<table class="table table-striped">
				<thead>
				<tr>
					<td class="left"><?php echo $entry_customer_group; ?></td>
					<td class="left"><?php echo $entry_quantity; ?></td>
					<td class="left"><?php echo $entry_priority; ?></td>
					<td class="left"><?php echo $entry_price; ?></td>
					<td class="left"><?php echo $entry_date_start; ?></td>
					<td class="left"><?php echo $entry_date_end; ?></td>
					<td></td>
				</tr>
				</thead>
				<?php $discount_row = 0; ?>
				<?php foreach ($product_discounts as $product_discount) { ?>
					<tbody id="discount_row<?php echo $discount_row; ?>">
					<tr>
						<td class="left"><?php echo $customer_groups[$product_discount['customer_group_id']]; ?></td>
						<td class="left"><?php echo $product_discount['quantity']; ?></td>
						<td class="left"><?php echo $product_discount['priority']; ?></td>
						<td class="left"><?php echo moneyDisplayFormat($product_discount['price']); ?></td>
						<td class="left"><?php echo $product_discount['date_start']; ?></td>
						<td class="left"><?php echo $product_discount['date_end']; ?></td>
						<td class="left">
							<a title="<?php echo $button_edit->text; ?>"
							   href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $update_discount); ?>"
							   class="btn" data-target="#discount_modal" data-toggle="modal"><i
										class="fa fa-edit fa-lg"></i></a>

							<a title="<?php echo $button_remove->text; ?>" data-confirmation="delete"
							   href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $delete_discount); ?>"
							   class="btn"><i class="fa fa-trash-o fa-lg"></i></a>
						</td>
					</tr>
					</tbody>
					<?php $discount_row++; ?>
				<?php } ?>
			</table>
		</div>
		<div class="panel-footer">
			<div class="row pull-right">
				<div class="col-sm-6 col-sm-offset-0">
					<a href="<?php echo $button_add_discount->href; ?>" data-target="#discount_modal"
					   data-toggle="modal">
						<button class="btn btn-primary">
							<i class="fa fa-plus"></i> <?php echo $button_add_discount->text; ?>
						</button>
					</a>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title"><?php echo $tab_special; ?></h4>
		</div>
		<div class="panel-body panel-body-nopadding">

			<table class="table">
				<thead>
				<tr>
					<td class="left"><?php echo $entry_customer_group; ?></td>
					<td class="left"><?php echo $entry_priority; ?></td>
					<td class="left"><?php echo $entry_price; ?></td>
					<td class="left"><?php echo $entry_date_start; ?></td>
					<td class="left"><?php echo $entry_date_end; ?></td>
					<td></td>
				</tr>
				</thead>
				<?php $discount_row = 0; ?>
				<?php foreach ($product_specials as $item) { ?>
					<tbody>
					<tr>
						<td class="left col-sm-6"><?php echo $customer_groups[$item['customer_group_id']]; ?></td>
						<td class="left"><?php echo $item['priority']; ?></td>
						<td class="left"><?php echo moneyDisplayFormat($item['price']); ?></td>
						<td class="left"><?php echo $item['date_start']; ?></td>
						<td class="left"><?php echo $item['date_end']; ?></td>
						<td class="left">
							<a title="<?php echo $button_edit->text; ?>"
							   href="<?php echo str_replace('%ID%', $item['product_special_id'], $update_special); ?>"
							   class="btn"
							   data-target="#discount_modal" data-toggle="modal"><i class="fa fa-edit fa-lg"></i></a>

							<a title="<?php echo $button_remove->text; ?>"
							   class="btn" data-confirmation="delete"
							   href="<?php echo str_replace('%ID%', $item['product_special_id'], $delete_special); ?>"
									><i class="fa fa-trash-o fa-lg"></i></a>


						</td>
					</tr>
					</tbody>
					<?php $discount_row++; ?>
				<?php } ?>
			</table>
		</div>
		<div class="panel-footer">
			<div class="row pull-right">
				<div class="col-sm-6 col-sm-offset-0">
					<a href="<?php echo $button_add_special->href; ?>" data-target="#discount_modal"
					   data-toggle="modal">
						<button class="btn btn-primary">
							<i class="fa fa-plus"></i> <?php echo $button_add_special->text; ?>
						</button>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'discount_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>