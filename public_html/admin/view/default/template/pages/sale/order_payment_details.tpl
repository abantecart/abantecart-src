<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $order_tabs ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			<a class="btn btn-white tooltips" target="_invoice" href="<?php echo $invoice_url; ?>" data-toggle="tooltip"
			   title="<?php echo $text_invoice; ?>" data-original-title="<?php echo $text_invoice; ?>">
				<i class="fa fa-file-text"></i>
			</a>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>
	
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $title_payment_details; ?></label>

		<?php echo $this->getHookVar('extension_payment_details'); ?>

	</div>

</div>
