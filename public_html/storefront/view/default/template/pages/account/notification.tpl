<h1 class="heading1">
  <span class="maintext"><i class="fa fa-bullhorn"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>
<?php echo $form['form_open']; ?>
<div class="contentpanel">
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

	
	<div class="form-group">
	    <div class="col-md-12">
	    	<button class="btn btn-orange pull-right lock-on-click" title="<?php echo $form['continue']->name ?>" type="submit">
	    	    <i class="<?php echo $form['continue']->{'icon'}; ?> fa"></i>
	    	    <?php echo $form['continue']->name ?>
	    	</button>
	    	<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
	    	    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
	    	    <?php echo $form['back']->text ?>
	    	</a>
	    </div>
	</div>

</div>

</form>