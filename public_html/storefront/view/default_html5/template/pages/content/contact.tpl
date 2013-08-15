<h1 class="heading1">
  <span class="maintext"><i class="icon-envelope"></i> <?php echo $heading_title; ?></span>
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
	<h4 class="heading4"><?php echo $text_edit_address; ?></h4>

	<div class="content">
	  <div style="display: inline-block; width: 100%;">
	    <div style="float: left; display: inline-block; width: 49%;">
	    <b><?php echo $text_address; ?></b><br />
	      <?php echo $store; ?><br />
	      <?php echo $address; ?></div>
	    <div style="float: right; display: inline-block; width: 49%;">
	      <?php if ($telephone) { ?>
	      <b><?php echo $text_telephone; ?></b><br />
	      <?php echo $telephone; ?><br />
	      <br />
	      <?php } ?>
	      <?php if ($fax) { ?>
	      <b><?php echo $text_fax; ?></b><br />
	      <?php echo $fax; ?>
	      <?php } ?>
	    </div>
	  </div>
	</div>
	<div class="content">
	  <table width="100%">
	    <tr>
	      <td><?php echo $form_output; ?></td>
	    </tr>
	  </table>
	</div>

</div>