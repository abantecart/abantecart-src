<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-money-bill-transfer me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<div class="container-fluid">
	<table class="table table-striped transaction-table">
	<thead>
	<tr>
		<th><?php echo $text_transaction_id; ?></th>
		<th><?php echo $text_date_added; ?></th>
		<th><?php echo $text_transaction_type; ?></th>
		<th><?php echo $text_debit; ?></th>
		<th><?php echo $text_credit; ?></th>
		<th ><?php echo $text_transaction_description; ?></th>
	</tr>
	</thead>
	<?php if( sizeof((array)$transactions) ) { foreach ($transactions as $trn) { ?>
		<tr>
			<td><?php echo $trn['customer_transaction_id']; ?></td>
			<td><?php echo $trn['date_added']; ?></td>
			<td><?php echo $trn['transaction_type']; ?></td>
			<td><?php echo $trn['debit']; ?></td>
			<td><?php echo $trn['credit']; ?></td>
			<td class="col-md-4"><?php echo $trn['description']; ?></td>
		</tr>
		<?php echo $this->getHookVar('account_transactions_row_hook_var'); ?>
	<?php } } ?>
	</table>
	<?php if ( sizeof((array)$transactions) <=0 ) { ?>
		<div><?php echo $text_error; ?></div>
	<?php } ?>
	<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
</div>
<?php echo $this->getHookVar('account_transactions_hook_var'); ?>

<div class="container-fluid cart_total">
    <div class="cart-info totals pull-right">
        <table class="table table-striped table-bordered">
            <tr>
                <td>
                    <span class="fw-bold"><?php echo $text_total; ?></span>
                </td>
                <td class="text-end">
                    <span class="fw-bold"><?php echo $balance_amount; ?></span>
                </td>
            </tr>
        </table>
        <div class="col-12 p-3 d-flex align-content-stretch mb-2">
            <a href="<?php echo $continue; ?>" class="btn btn-secondary" title="<?php echo_html2view($button_continue->text); ?>">
                <i class="fa fa-arrow-right"></i>
                <?php echo $button_continue->text ?>
            </a>
            <?php echo $this->getHookVar('account_transactions_button_hook_var'); ?>
        </div>
    </div>
</div>
