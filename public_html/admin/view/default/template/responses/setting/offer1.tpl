<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title">Simply tax with Avalara Avatax service</h4>
</div>

<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<div class="col-md-12">
			<div class="text-center">
				<a href="https://buy.avalara.com/signup?partner=abantecart&CampaignID=7010b0000013ZLp&" target="_ext_help">
					<img width="900px" src="admin/view/default/image/avatax-trial-750x400.jpg" /></a>
			</div>

			<div class="text-center">
				<div class="btn-group">
					<a class="btn btn-white" href="https://buy.avalara.com/signup?partner=abantecart&CampaignID=7010b0000013ZLp&" target="_ext_help">
						<i class="fa fa-arrow-right"></i> Learn More
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="center">
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