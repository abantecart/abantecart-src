<div id="<?php echo $id ?>_fileupload" class="fileupload">
	<input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="" <?php echo $attr ?> />
	<span class="file_element">
		<div class="aform">
	        <div class="afield mask1">
	            <div class="cl">
	                <div class="cr">
	                    <div class="cc" style="width: 300px;"><div class="atext default_text"><?php echo $default_text;?></div></div>
	                </div>
	            </div>
	        </div>
	    </div>
	</span>
</div>
<?php if ($required == 'Y') : ?>
<span class="required">*</span>
<?php endif; ?>
<script type="text/javascript">
$('#<?php echo str_replace(']','\\\]',str_replace('[','\\\[',$id)) ?>').live('change',function(){
	$(this).next().find('.atext').html($(this).val()).removeClass('default_text');
});
</script>
