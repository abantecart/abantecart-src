<div class="header_logo"><a href="<?php echo $home; ?>"><img src="<?php echo $template_dir; ?>image/logo.png" title="<?php echo $heading_title; ?>" /></a></div>
  <div class="section1">
    <?php if ($logged) { ?>
    
    <div class="search_box flt_right">
      <div class="cl"><div class="cr"><div class="cc">
      <form id="searchform" action="<?php echo $search_action; ?>" method="post">
        <span class="icon_search">&nbsp;</span><input type="text" name="search" value="<?php echo $search_everywhere; ?>" onFocus="if(this.value == '<?php echo $search_everywhere; ?>') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'Search Everywhere';}" /><a onClick="$('#searchform').submit();" class="btn_search btn_standard"><?php echo $button_go; ?></a>
      </form>
      </div></div></div>
    </div>    
      <?php if ($languages) { ?>
    <div class="language_box flt_right">      
      <div class="cl"><div class="cr"><div class="cc">
      <form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" enctype="multipart/form-data" id="language_form">
        <div class="switcher">
          <?php foreach ($languages as $language) { ?>
          <?php if ($language['code'] == $language_code) { ?>
          <div class="selected"><a><img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" />&nbsp;&nbsp;<span><?php echo $language['name']; ?></span></a></div>
          <?php } ?>
          <?php } ?>
          <div class="option">
            <?php foreach ($languages as $language) { ?>
            <a onClick="$('input[name=\'language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#language_form').submit();"><img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" />&nbsp;&nbsp;<?php echo $language['name']; ?></a>
            <?php } ?>
          </div>
          <input type="hidden" name="language_code" value="" />
          <input type="hidden" name="redirect" value="<?php echo str_replace('&', '&amp;', $redirect); ?>" />
        </div>
      </form>
      </div></div></div>
    </div>
      <?php } ?>
    <div class="store_nav flt_right">    
    <a onClick="window.open('<?php echo $store; ?>');"><?php echo $text_front; ?></a>&nbsp;|&nbsp;<?php echo $logged; ?> (<a class="top" href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>)
    </div>
    
    <?php } ?>
  </div>
  <div class="section2">
    <?php if ($logged) {
    	 ?>
    <div class="msg_box flt_right">
      <div class="msg_box_tl"><div class="msg_box_tr"><div class="msg_box_tc"></div></div></div>
      <div class="msg_box_cl"><div class="msg_box_cr"><div class="msg_box_cc">
	      <?php echo $ant; ?>
      </div></div></div>
      <div class="msg_box_bl"><div class="msg_box_br"><div class="msg_box_bc"></div></div></div>
    </div>
    
    <a href="<?php echo  $messages_link ?>"><div>
    <?php
    if($new_messages){  
    		  	
    		if($new_messages['N']>0){
    		?><div class="n_msg_box" id="notice_msg_box"><div id="notice_msg_cnt" class="msg_count"><?php echo $new_messages['N']; ?></div></div>
    	   	<?php
    		}
    		if($new_messages['W']>0){
    	   	?> <div class="w_msg_box" id="warning_msg_box"><div id="warning_msg_cnt" class="msg_count"><?php echo $new_messages['W']; ?></div></div>
    	   	<?php 
    		}
    		if($new_messages['E']>0){    			
    	   	?> <div class="e_msg_box" id="error_msg_box"><div id="error_msg_cnt" class="msg_count"><?php echo $new_messages['E']; ?></div></div>
    	   	<?php }
    	   	 ?>
	<?php } ?>
	</div></a>
    <?php } ?>
  </div>
  
  <?php if ($logged) { ?>
  <div id="menu_box"><?php echo $menu; ?></div>
  <?php } ?>
  
  <?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php } ?>