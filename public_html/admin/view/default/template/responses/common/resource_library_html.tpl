<?php foreach ( $types as $type ) { ?>
    <div class="fieldset" id="type_<?php echo $type['type_name']; ?>" style="display:none">
        <div class="heading"><a id="tab_<?php echo $type['type_name']; ?>"><?php echo ${'text_type_'.$type['type_name']}; ?></a></div>
        <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
        <div class="cont_left"><div class="cont_right"><div class="cont_mid">
	        <div class="back2top"><a href="<?php echo $current_url; ?>#top"><?php echo $text_on_top; ?></a></div>
            <table class="image_list">
            <tbody>
                <tr>
                    <td class="type_blocks"></td>
                </tr>
            </tbody>
            </table>
        </div></div></div>
        <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
<?php } ?>
<div id="confirm_unmap_dialog" title="<?php echo $confirm_unmap_title ?>" style="display:none">
    <?php echo $text_confirm_unmap ?>
</div>
<div id="confirm_del_dialog" title="<?php echo $confirm_del_title ?>" style="display:none">
    <?php echo $text_confirm_del ?>
</div>