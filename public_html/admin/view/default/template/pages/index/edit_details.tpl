<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="row">
<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">

		<?php echo $form['form_open']; ?>
		<div class="fieldset">
		  <div class="heading"><?php echo $form_title; ?></div>
	          <table class="form">
	            <?php foreach ($form['fields'] as $name => $field) { ?>
					<tr>
						<td><?php echo ${'entry_'.$name}; ?></td>
						<td id="payment_<?php echo $name; ?>">
							<?php echo $field; ?>
							<?php if (!empty($error[$name])) { ?>
								<div class="field_err"><?php echo $error[$name]; ?></div>
							<?php } ?>
						</td>
					</tr>
				<?php } //foreach ($form['fields'] as $name => $field)  ?>
	          </table>
	 	</div><!-- <div class="fieldset"> -->
	 	
		<div class="buttons align_center">
		  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
		  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
	    </div>
		</form>

  		</div>
  	</div>
</div>
</div>