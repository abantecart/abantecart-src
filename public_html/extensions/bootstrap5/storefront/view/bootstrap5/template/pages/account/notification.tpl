<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-bell me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<?php echo $form['form_open']; ?>
<div class="container-fluid">
	<table class="table table-striped notification-table">
		<thead>
			<tr>
				<th><?php echo $text_sendpoint; ?></th>
				<?php foreach($protocols as $protocol){?>
					<th><?php echo $protocol['title']; ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach($form['fields']['sendpoints'] as $sendpoint){ ?>
			<tr>
				<td><?php echo $sendpoint['title']; ?><br>
					<small><?php echo $sendpoint['note']; ?></small>
				<?php
				if($sendpoint['warn']){ ?>
					<p class="alert-danger"><?php echo $sendpoint['warn']; ?></p>
				<?php } ?>
				</td>
				<?php foreach($protocols as $protocol){?>
					<td><?php echo $sendpoint['values'][$protocol['name']];	?></td>
				<?php } ?>


		<?php } ?>
		</tbody>

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

    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
        <a href="<?php echo $back; ?>" class="btn btn-secondary" title="<?php echo $form['back']->text ?>">
            <i class="<?php echo $form['back']->icon; ?>"></i>
            <?php echo $form['back']->text ?>
        </a>
        <button id="submit_button" type="submit"
                role="button"
                class="btn btn-primary ms-auto lock-on-click"
                title="<?php echo_html2view($form['continue']->name); ?>">
            <i class="fa <?php echo $form['continue']->icon; ?>"></i>
            <?php echo $form['continue']->name ?>
        </button>
    </div>
</div>

</form>