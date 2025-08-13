<?php if(!$zone_only){?>
    <div class="input-group mb-3">
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
    <?php } ?>
    </div>
<?php
}?>
<div class="input-group ">
	<select name="<?php echo $name ?>_zones[]"
            id="<?php echo $id ?>_zones"
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
<?php } ?>
</div>
<script type="text/javascript">
    <?php
    $qry = $submit_mode == 'id' ? "&country_id=" : "&country_name=";
    ?>
    const countryElm = <?php
            if($zone_only){
                echo '$("#'.$id.'_zones").parents("form").find("[name*=country]");'.PHP_EOL;
            }else{
                echo '$("#' . $id .'");'.PHP_EOL;
            }?>
        countryElm.change( function(){
            $('#<?php echo $id ?>_zones').load(
                '<?php echo $url. $qry ?>'
                + encodeURIComponent($(this).val())
                + '&'+encodeURIComponent('zone_name=<?php echo $zone_name; ?>')
            );
        }).change();
</script>