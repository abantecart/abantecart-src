<textarea 
    class="form-control atext <?php echo $style ?>"
    name="<?php echo $name ?>"
    placeholder="<?php echo $placeholder; ?>"
    id="<?php echo $id ?>"
    data-orgvalue="<?php echo $ovalue ?>"
    <?php echo $attr ?>
>
<?php echo $value ?>
</textarea>

<?php if ( $required == 'Y' || $multilingual  || !empty ($help_url) || $history_url ) { ?>
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
<?php }

if($label_text){
    echo $label_text;
} ?>
