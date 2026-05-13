<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php }
if($icon){?>
    <div class="input-group-text" title="<?php echo_html2view($display_name);?>"><?php echo $icon; ?></div>
<?php }?>
    <div class="form-check-inline d-flex flex-wrap form-control me-0">
        <input type="file" name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> class="form-control <?php echo $style?>" <?php if ( $required ) { echo 'required'; }?>/>
        <?php if (is_array($value) && !empty($value['path'])) {
            $fileUrl = HTTPS_SERVER . ltrim(str_replace(DIR_ROOT, '', $value['path']), '/');
            $fileName = basename($value['path']);
        ?>
        <div class="w-100 mt-1">
            <small class="text-muted"><a href="<?php echo_html2view($fileUrl); ?>" target="_blank"><?php echo_html2view($fileName); ?></a></small>
        </div>
        <?php } ?>
    </div>
<?php if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>