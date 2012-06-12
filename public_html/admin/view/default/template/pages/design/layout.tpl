<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<?php echo $text_select_template; ?><select id="layout_template">
<?php
foreach($templates as $t) {
    echo '<option '.($tmpl_id == $t ? 'selected' : '').'  value="'.$t.'">'.$t.'</option>';
}
?>
</select>
<br class="clr_both "/>
<br/>

<div class="flt_left">
  <ul id="page_links">
    <?php foreach($pages as $pg) { ?>
    <?php if(!empty($pg['name'])) { ?>
    <li><a href="<?php echo $page_url . '&tmpl_id='.$tmpl_id.'&page_id='.$pg['page_id'].'&layout_id='.$pg['layout_id'] ; ?>"
          <?php echo ($pg['page_id'] == $page['page_id'] && $pg['layout_id'] == $page['layout_id']  ? 'class="shover"' : '')?>
           title="<?php echo $pg['layout_name']; ?>"><?php echo $pg['name']; ?></a></li>
    <?php } ?>
    <?php } ?>
  </ul>
</div>
<div id="page_layout">
	<?php echo $layoutform; ?>
</div>
<script><!--
jQuery(function($){

    $('#layout_template').width('150')
        .aform({
            triggerChanged: false,
        })
        .change(function(){
            window.location = '<?php echo $page_url?>&tmpl_id='+this.value;
        });
    $.aform.styleGridForm('#layout_template');

});
--></script>