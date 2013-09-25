<?php if ($success) { ?>
	<h1 class="heading1">
	  <span class="maintext"><i class="icon-thumbs-up"></i> <?php echo $text_subscribe_register; ?></span>
	  <span class="subtext"></span>
	</h1>

	<div class="container-fluid">

	<section class="mb40">
		<p></p>
		<p><?php echo $success; ?></p>
	</section>
	</div>



	<div class="control-group">
		<div class="controls">
			<div class="pull-right span2 mt20 mb40">
				<?php echo $continue;?>
			</div>
		</div>
	</div>

<?php }else{ ?>
	<h1 class="heading1">
	  <span class="maintext"><i class="icon-group"></i> <?php echo $text_subscribe_register; ?></span>
	  <span class="subtext"></span>
	</h1>
<?php if ($error_warning) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="container-fluid">
	<?php echo $form['form_open']; ?>
	
	<p><?php echo $text_account_already; ?></p>
	
	<h4 class="heading4"><?php echo $text_your_details; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
		<?php
			$field_list = array();
			array_push($field_list, 'firstname', 'lastname', 'email');
			foreach ($field_list as $field_name) {
		?>
			<div class="control-group <?php echo (${'error_'.$field_name} ? 'error' : '')?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form[$field_name]; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php } ?>
		</fieldset>
	</div>

	<?php echo $this->getHookVar('subscriber_hookvar'); ?>
	


	<div class="control-group">
	    <div class="controls">
			<?php echo $form['subscriber'];	?>
			<div>
				<a id="create_full_account" href="#"><?php echo $subscriber_switch_text_full; ?></a>
			</div>


	<?php if ($text_agree) { /*?>
			<label class="span6 mt20 mb40 <?php echo $subscriber? 'hide' :''?>">
				<?php echo $text_agree; ?><a href="<?php echo $text_agree_href; ?>" onclick="openModalRemote('#privacyPolicyModal','<?php echo $text_agree_href; ?>'); return false;"><b><?php echo $text_agree_href_text; ?></b></a>

				<?php echo $form['agree']; ?>
			</label>

	<?php */ } ?>
	    	<div class="pull-right span2 mt20 mb40">
	    		<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    		    <i class="icon-ok icon-white"></i>
	    		    <?php echo $form['continue']->name ?>
	    		</button>
	    	</div>	
	    </div>
	</div>
	
</form>
</div>

<div id="privacyPolicyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="privacyPolicyModalLabel"><?php echo $text_agree_href_text; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>

<script type="text/javascript"><!--

$('#create_full_account').click(function(){

	$('#SubscriberFrm').attr('action','<?php echo $action_full; ?>');
	$('#SubscriberFrm').submit();
	return false;
});
//--></script>
<?php } ?>