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
    <div class="uneditable-input span2"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"><?php echo $placeholder ?></span></div>
    	<span class="btn btn-file">
    		<span class="fileupload-new"><i class="icon-file"></i> <?php echo $text_browse; ?></span>
    		<span class="fileupload-exists"><i class="icon-edit"></i></span>
    		<input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
    	</span>
    	<a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><i class="icon-remove"></i> </a>

	<?php if ( $required == 'Y' ) : ?>
	<span class="add-on required">*</span>
	<?php endif; ?>
</div>