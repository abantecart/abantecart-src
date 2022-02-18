<div class="form-group action-buttons text-center">
    <a id="<?php echo $button_back->name ?>" href="<?php echo $button_back->href; ?>" class="btn btn-default mr10 pull-left" title="<?php echo $button_back->text ?>">
    	<i class="fa fa-arrow-left"></i>
    	<?php echo $button_back->text ?>
    </a>
    <a id="checkout_btn" class="btn btn-orange lock-on-click" href="<?php echo $button_confirm->href; ?>" title="<?php echo $button_confirm->name ?>" >
        <i class="fa fa-check"></i>
        <?php echo $button_confirm->name; ?>
    </a>
</div>

