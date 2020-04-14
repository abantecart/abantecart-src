<form id="local_delivery" method="post">
    <div class="form-inline">
    <label class="checkbox"><?php echo $text_telephone; ?></label>
        <div class="input-group col-md-3">
        <?php echo $telephone; ?>
        </div>
        <button id="apply_telephone_btn" title="<?php echo $text_apply?>" class="btn btn-default mr10" value="telephone" type="submit">
                <i class="fa fa-check"></i> <?php echo $text_apply;?></button>

</div>
<div class="form-inline">
    <label></label>
    <div class="col-md-3">
        <?php if($error_telephone){?>
            <span class="help-block red"><?php echo $error_telephone; ?></span>
        <?php } ?>
    </div>
</div>
</form>
</br>