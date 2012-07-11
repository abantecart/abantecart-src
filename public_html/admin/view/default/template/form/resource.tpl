<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" id="<?php echo $id; ?>" />
<?php if($preview){ ?>
<img src="<?php echo $preview; ?>" alt="preview" id="<?php echo $id; ?>preview" class="image" onclick="image_upload_<?php echo $id; ?>('<?php echo $id; ?>', '<?php echo $id; ?>preview');" />
<?php } ?>