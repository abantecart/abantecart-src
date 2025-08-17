<?php if(!$no_wrapper){ ?>
    <div class="input-group flex-nowrap">
        <?php }
        if($icon){?>
            <div class="input-group-text" title="<?php echo_html2view($display_name);?>"><?php echo $icon; ?></div>
        <?php } ?>
        <textarea class="form-control <?php echo $style; ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $attr; ?> <?php if ( $required ) { echo 'required'; }?> ><?php echo $value ?></textarea>
        <?php if ( $required ){ ?>
        <span class="input-group-text text-danger">*</span>
        <?php }
    if(!$no_wrapper){ ?>
    </div>
<?php } ?>