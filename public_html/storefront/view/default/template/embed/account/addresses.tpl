<h1 class="heading1">
  <span class="maintext"><i class="fa fa-book"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<?php if ($success) { ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $success; ?>
</div>
<?php } ?>

<?php if ($error_warning) { ?>
<div class="alert alert-error alert-danger">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<?php echo $error_warning; ?>
</div>
<?php } ?>

<div class="contentpanel">

	<h4 class="heading4"><?php echo $text_address_book; ?></h4>
    <?php foreach ($addresses as $result) { ?>
    <div class="genericbox border-bottom">
      <table width="100%">	
        <tr>
          <td><address><?php echo $result['address']; ?></address></td>
          <td class="pull-right">
          	<?php echo $result['button_edit'];
          		if ( !$result['default'] ) {
          			echo $result['button_delete'];
          		}
          	?></td>
        </tr>
      </table>
    </div>
    <?php } ?>

	<div class="col-md-12 col-xs-12 pull-right mt20 mb20">
		<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $button_back->text ?>">
		    <i class="<?php echo $button_back->{'icon'}; ?>"></i>
		    <?php echo $button_back->text ?>
		</a>
		<a href="<?php echo $insert;  ?>" class="btn btn-orange pull-right" title="<?php echo $button_insert->text ?>">
		    <i class="<?php echo $button_insert->{'icon'}; ?>"></i>
		    <?php echo $button_insert->text ?>
		</a>
	</div>

</div>
