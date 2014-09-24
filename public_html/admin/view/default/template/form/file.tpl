<div id="<?php echo $id ?>_fileupload" class="text_element fileupload" style="display: inline-block;">
    <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="" <?php echo $attr ?> />
	<span class="file_element">
		<div class="aform">
            <div class="afield mask1">
                <div class="cl">
                    <div class="cr">
                        <div class="cc" style="width: 290px;">
                            <div class="atext default_text"><?php echo $default_text;?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</span>
    <a id="browse_<?php echo $id ?>" class="aform btn_standard" style="vertical-align: middle;"
       title="<?php echo $default_text;?>">
        <span class="button3"><span><?php echo $text_browse ?></span></span>
    </a>
</div>
<?php if ($required == 'Y') : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>
<script type="text/javascript">
    $('#<?php echo $id ?>').on('change', function () {
        $(this).next().find('.atext').html($(this).val()).removeClass('default_text');
    });
</script>
