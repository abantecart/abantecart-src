<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
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
    <?php if(!empty($pg['name'])) { 
    	$uri = '&tmpl_id='.$tmpl_id.'&page_id='.$pg['page_id'].'&layout_id='.$pg['layout_id'];
    ?>
    <li><a href="<?php echo $page_url . $uri; ?>"
          <?php echo ($pg['page_id'] == $page['page_id'] && $pg['layout_id'] == $page['layout_id']  ? 'class="shover"' : '')?>
           title="<?php echo $pg['name']; ?>"><?php echo $pg['layout_name']; ?></a>
           <?php if(empty($pg['restricted'])) { ?>
    	   <a data-delete-url="<?php echo $page_delete_url . $uri; ?>" class="delete_page_layout close"><i class="icon-trash"></i></a>
    	   <?php } ?>
    </li>
    <?php } ?>
    <?php } ?>
  </ul>
</div>
<div id="page_layout">
	<?php echo $layoutform; ?>
</div>
<script type="text/javascript"><!--

    $('#layout_template').width('150')
        .aform({
            triggerChanged: false
        })
        .change(function(){
            window.location = '<?php echo $page_url?>&tmpl_id='+this.value;
        });
    $.aform.styleGridForm('#layout_template');
    
    $('.delete_page_layout').click(function() {
    	if ( confirm('<?php echo $text_delete_confirm; ?>' )) {
    		var url = $(this).attr('data-delete-url');
    		window.location = url + '&confirmed_delete=yes';	
    	}
    });
    
--></script>