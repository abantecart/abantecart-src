<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title">PCI complinet merchant service - CardConnect</h4>
</div>

<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<div class="col-md-12">
			<div class="text-center">
				<a href="https://cardconnect.com/partner/abantecart" target="_ext_help">
					<img width="900px" src="admin/view/default/image/CardConnect728x90.png" />
				</a>
			</div>
			<p>
				<label><b>Single Source Solution</b></label>
				<br />
				With CardConnect for AbanteCart, you can manage your business and process payments securely, all within one platform. Your company will be equipped to accept credit, debit, check, gift card and loyalty transactions that can be easily reconciled inside your current software, creating the perfect package featuring a simple sign up, 24/7 support and next day funding among other features to help you run your business.
				<br />
				Ease Of Sign Up (CoPilot API)
				<br />
				Setting up a merchant services account can be daunting, but with CardConnect for AbanteCart you’ll be up and running in no time! Simply fill out an application to start the process, and a representative will reach out within 24 hours!
				<br />
			</p>
			<p>
				<label><b>Pricing</b></label>
				<br />
				With CardConnect for AbanteCart, you have access to next day funding, ensuring funds show up in your bank account in less than 24 hours. And with low pricing and easy to read statements, you’ll always know exactly what’s on your bill each and every month.
			</p>
			<p>
				<label><b>Support</b></label>
				<br />
				Together, CardConnect for AbanteCart offer simple and secure processing with a reliable support team to make sure you’re business is always running smoothly.
				<br />
				Never miss a sale with CardConnect’s 24/7 support! If you have a question about your terminal or a previous transaction,  our in-house Merchant Solutions team is available to provide you the information you need to seamlessly accept payments.
				<br />
				Transaction Reporting and Management
				<br />
				CardConnect for AbanteCart gives you access to comprehensive transaction management and reporting on your computer or mobile device. Complete refunds on the go, and track every sale from the time it’s swiped to the time it’s deposited into your bank account. You can even manage multiple locations, all from one login!
			</p>
			<p>
				<label><b>Security</b></label>
				<br />
				Protect your business and your customers with CardConnect’s payment security technology.  The powerful combination of point-to-point encryption (P2PE) and patented tokenization
				technology means you no longer have to worry about going through a crippling data breach.
			</p>
			<p>
				<label><b>How it works</b></label>
				Before a customer’s payment information can enter your business’s system, application, or computer, it will be stored within a tokenizer.
				<br />
				The tokenizer then returns an irreversible string of data called a token, which can be used to process payments while drastically reducing your company’s PCI scope.
				<br />
				Did you know..When combined with P2PE technology, your business and your customers are protected.
				<br />
				[Bonus] Not only does this protect you from cyber criminals, it saves time by drastically reducing the amount of time you spend filling out PCI questionnaires.
			</p>
			<div class="text-center">
				<div class="btn-group">
					<a class="btn btn-white" href="https://cardconnect.com/partner/abantecart" target="_ext_help">
						<i class="fa fa-arrow-right"></i> Learn More
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="center">
				<?php if (!empty ($help_url)) { ?>
				<div class="btn-group">
					<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip" data-original-title="<?php echo $text_external_help; ?>">
						<i class="fa fa-question-circle fa-lg"></i>
					</a>
				</div>
				<?php } ?>

				<?php if ($back) { ?>
				<div class="btn-group">
					<a class="btn btn-white step_back" href="<?php echo $back; ?>">
						<i class="fa fa-arrow-left"></i> <?php echo $button_back; ?>
					</a>
				</div>
				<?php } ?>
				<button class="btn btn-primary">
					<i class="fa fa-save"></i> <?php echo $text_next; ?> <i class="fa fa-arrow-right"></i>
				</button>
			</div>
		</div>
	</div>
	</form>
</div>

<?php include('quick_start_js.tpl'); ?>