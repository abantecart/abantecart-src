<?php if(!$zone_only){
    if(!$no_wrapper){?>
    <div class="input-group mb-3">
<?php }
if($icon){?>
    <div class="input-group-text"><?php echo $icon; ?></div>
<?php }?>
        <select name="<?php echo $name ?>[]"
                id="<?php echo $id ?>"
                class="form-select <?php echo $style; ?>"
                data-placeholder="<?php echo $placeholder ?>"
                <?php echo $attr ?>>
        <?php foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?> >
                <?php echo $text ?>
            </option>
        <?php } ?>
        </select>
    <?php if ( $required ){ ?>
        <span class="input-group-text text-danger">*</span>
    <?php }
    if(!$no_wrapper){ ?>
    </div>
<?php
    }
}

if(!$no_wrapper){?>
<div class="input-group ">
<?php }
if($icon){?>
    <div class="input-group-text"><?php echo $icon; ?></div>
<?php }

    if(!$zone_only){
        $name .= '_zones[]';
        $zoneElmId = $id.'_zones';
    }else{
        $zoneElmId = $id;
    }
?>
	<select name="<?php echo $name ?>"
            id="<?php echo $zoneElmId ?>"
            class="form-select <?php echo $style; ?>"
            data-placeholder="<?php echo $placeholder ?>">
<?php foreach ( $zone_options as $v => $text ) { ?>
        <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$zone_value) ? ' selected="selected" ':'') ?> >
            <?php echo $text ?>
        </option>
<?php } ?>
	</select>
<?php if ( $required ){ ?>
    <span class="input-group-text text-danger">*</span>
<?php }
if(!$no_wrapper){ ?>
</div>
<?php } ?>
<script type="text/javascript">
    <?php
    $qry = $submit_mode == 'id' ? "&country_id=" : "&country_name=";
    ?>
    const countryElm = <?php
            if($zone_only){
                echo '$("#'.$zoneElmId.'").parents("form").find("[name*=country]");'.PHP_EOL;
            }else{
                echo '$("#' . $zoneElmId .'");'.PHP_EOL;
            }?>
        countryElm.change( function(){
            $('#<?php echo $id ?>').load(
                '<?php echo $url. $qry ?>'
                + encodeURIComponent($(this).val())
                + '&'+encodeURIComponent('zone_name=<?php echo $zone_name; ?>')
            );
        });
</script>