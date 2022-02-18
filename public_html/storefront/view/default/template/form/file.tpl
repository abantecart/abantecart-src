<span class="btn btn-file">
    <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> class="pull-left <?php echo $style?>"/>
	<?php if ( $required == 'Y' ){ ?>
	<span class="form-inline required">*</span>
	<?php } ?>
</span>