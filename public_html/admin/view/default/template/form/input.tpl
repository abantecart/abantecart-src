<?php if ( $type == 'password' && $has_value == 'Y' && $required) { ?>
	<div class="input-group-addon confirm_default" id="<?php echo $id ?>_confirm_default">***********</div>
<?php } ?>
    <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="form-control atext <?php echo $style; ?>" value="<?php echo $value ?>" data-orgvalue="<?php echo $value ?>" <?php echo $attr; ?> placeholder="<?php echo $placeholder ?>" />

<?php if ( $required == 'Y' || $multilingual || !empty ($help_url) || $history_url ) { ?>
    <span class="input-group-addon">
    <?php if ( $required == 'Y') { ?>
        <span class="required">*</span>
    <?php } ?>

    <?php if ( $multilingual ) { ?>
    <span class="multilingual"><i class="fa fa-language"></i></span>
    <?php } ?>

    <?php if ( !empty ($help_url) ) { ?>
    <span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
    <?php } ?>

    <?php if($history_url){ ?>
        <a title="<?php echo_html2view($button_field_history); ?>"
           data-original-title="<?php echo_html2view($button_field_history); ?>"
           href="<?php echo $history_url ?>" data-target="#hist_modal" data-toggle="modal"
           class="tooltips view_history ml10">
            <i class="fa fa-history fa-fw"></i>
        </a>
    <?php } ?>

    </span>
<?php } ?>
