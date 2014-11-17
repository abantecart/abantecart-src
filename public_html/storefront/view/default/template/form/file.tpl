<?php
	//Add styles and jafascript for boootstrap file upload
	/**
	 * @var ADocument $doc
	 */
	$doc = $this->registry->get('document');
	$doc->addStyle(array(
	    'href' => $this->templateResource('/stylesheet/bootstrap-fileupload.css'),
	    'rel' => 'stylesheet',
	    'media' => 'screen',
	));	
	$doc->addScriptBottom($this->templateResource('/javascript/bootstrap-fileupload.min.js'));
?>
<div class="fileupload fileupload-new file_element" data-provides="fileupload">
    <div class="uneditable-input col-md-2"><i class="fa fa-file fileupload-exists"></i> <span class="fileupload-preview"><?php echo $placeholder ?></span></div>
    	<span class="btn btn-file">
    		<span class="fileupload-new"><i class="fa fa-file"></i> <?php echo $text_browse; ?></span>
    		<span class="fileupload-exists"><i class="fa fa-edit"></i></span>
    		<input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
    	</span>
    	<a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><i class="fa fa-remove"></i> </a>

	<?php if ( $required == 'Y' ){ ?>
	<span class="required">*</span>
	<?php } ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	$("#<?php echo $id; ?>").fileinput({
	    overwriteInitial: false,
	    browseClass: 'btn btn-info',
	    showUpload: false,
	    showCaption: true,
	    showPreview: false,
	    browseIcon: '<i class="fa fa-folder-open fa-fw"></i> ',
	    browseLabel: '<?php echo $text_browse ?>',
	    initialCaption: '<?php echo $default_text ?>'
	});
});
</script>