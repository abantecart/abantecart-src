<h1 class="heading1">
  <span class="maintext"><i class="icon-group"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

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
			if ($noemaillogin) { array_push($field_list, 'loginname'); }
			array_push($field_list, 'firstname', 'lastname', 'email', 'telephone', 'fax');
			foreach ($field_list as $field_name) {
		?>
			<div class="control-group <?php echo ${'error_'.$field_name} ? 'error' : ''; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form[$field_name]; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
		</fieldset>
	</div>

	<h4 class="heading4"><?php echo $text_your_address; ?></h4>
	<div class="registerbox form-horizontal ">
		<fieldset>
		<?php
			$field_list = array('company' => 'company', 
								'address_1' => 'address_1', 
								'address_2' => 'address_2', 
								'city' => 'city',
								'postcode' => 'postcode',
								'country' => 'country_id', 
								'zone' => 'zone_id',
								);
			
			foreach ($field_list as $field_name => $field_id) {
		?>
			<div class="control-group <?php if (${'error_'.$field_name}) echo 'error'; ?>">
				<label class="control-label"><?php echo ${'entry_'.$field_name}; ?></label>
				<div class="controls">
				    <?php echo $form[$field_id]; ?>
					<span class="help-inline"><?php echo ${'error_'.$field_name}; ?></span>
				</div>
			</div>		
		<?php
			}
		?>	
		</fieldset>
	</div>
	
	<h4 class="heading4 "><?php echo $text_your_password; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
			<div class="control-group <?php if ($error_password) echo 'error'; ?>">
				<label class="control-label"><?php echo $entry_password; ?></label>
				<div class="controls">
				    <?php echo $form['password']; ?>
					<span class="help-inline"><?php echo $error_password; ?></span>
				</div>
			</div>
			<div class="control-group <?php if ($error_confirm) echo 'error'; ?>">
				<label class="control-label"><?php echo $entry_confirm; ?></label>
				<div class="controls">
				    <?php echo $form['confirm']; ?>
					<span class="help-inline"><?php echo $error_confirm; ?></span>
				</div>
			</div>
		</fieldset>
	</div>
	
	<?php echo $this->getHookVar('customer_attributes'); ?>
	
	<h4 class="heading4 "><?php echo $text_newsletter; ?></h4>
	<div class="registerbox form-horizontal">
		<fieldset>
			<div class="control-group">
				<label class="control-label"><?php echo $entry_newsletter; ?></label>
				<div class="controls">
				    <?php echo $form['newsletter']; ?>
				</div>
			</div>
		</fieldset>
	</div>


	<div class="control-group">
	    <div class="controls">
	<?php if ($text_agree) { ?>
			<label class="span6 mt20 mb40">
				<?php echo $text_agree; ?><a href="<?php echo $text_agree_href; ?>" onclick="openModalRemote('#privacyPolicyModal','<?php echo $text_agree_href; ?>'); return false;"><b><?php echo $text_agree_href_text; ?></b></a>

				<?php echo $form['agree']; ?>
			</label>

	<?php } ?>    	
	    	<div class="span2 mt20 mb40">
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
$('#AccountFrm_country_id').change( function(){
    $('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id=' + $(this).val() + '&zone_id=<?php echo $zone_id; ?>');
});
$('select[name=\'zone_id\']').load('index.php?rt=common/zone&country_id='+ $('#AccountFrm_country_id').val() +'&zone_id=<?php echo $zone_id; ?>');

//--></script>