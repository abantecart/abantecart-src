<?php
if(!$no_wrapper){?>
<div class="input-group h-100 flex-nowrap">
<?php }
if($icon){?>
    <div class="input-group-text" title="<?php echo_html2view($display_name);?>"><?php echo $icon; ?></div>
<?php } ?>
        <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?>
               class="w-100 form-control form-check-inline d-flex flex-nowrap  me-0 <?php echo $style?>" <?php if ( $required ) { echo 'required'; }?>/>
<?php if ( $required ) { ?>
    <span class="d-inline-flex input-group-text text-danger py-auto border-0">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php }
if (is_array($value) && !empty($value['path'])) {
    $fileUrl = HTTP_SERVER . ltrim(str_replace(DIR_ROOT, '', $value['path']), '/');
    $fileName = basename($value['path']);
?>
<div class="w-100 mt-1">
    <small class="text-muted"><a href="<?php echo_html2view($fileUrl); ?>" target="_blank"><?php echo_html2view($fileName); ?></a></small>
</div>
<?php } ?>