<?php if ( $type == 'password' && $has_value == 'Y' && $required) { ?>
	<div class="input-group-addon confirm_default" id="<?php echo $id ?>_confirm_default">***********</div>
<?php } ?>
    <input id="<?php echo $id; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" 
           class="form-control atext <?php echo $style; ?>" value="<?php echo $value ?>" 
           data-orgvalue="<?php echo $value ?>" <?php echo $attr; ?> 
           placeholder="<?php echo $placeholder ?>" <?php if($list){ echo ' list="'.$id.'_list"'; } ?>/>
<?php
    if($list){ ?>
        <datalist id="<?php echo $id.'_list'?>">
            <?php foreach((array)$list as $l) {
                echo '<option value="' . html2view($l) . '">';
            }?>
        </datalist>
    <?php }
if ( $required || $multilingual || $help_url || $history_url ) { ?>
    <span class="input-group-addon">
    <?php if ( $required) { ?>
        <span class="required">*</span>
    <?php }
    if ( $multilingual ) { ?>
        <span class="multilingual"><i class="fa fa-language"></i></span>
    <?php }
    if ( $help_url ) { ?>
        <span class="help_element">
            <a href="<?php echo $help_url; ?>" target="new">
                <i class="fa fa-question-circle fa-lg"></i>
            </a>
        </span>
    <?php }
    if($history_url){ ?>
        <a title="<?php echo_html2view($button_field_history); ?>"
           data-original-title="<?php echo_html2view($button_field_history); ?>"
           href="<?php echo $history_url ?>" data-target="#hist_modal" data-toggle="modal"
           class="tooltips view_history ml10">
            <i class="fa fa-history fa-fw"></i>
        </a>
    <?php } ?>
    </span>
<?php } ?>
