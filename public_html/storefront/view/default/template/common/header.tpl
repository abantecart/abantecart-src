  <div class="h_section1">
    <?php if ( is_file( DIR_RESOURCE . $logo ) ) {  ?>
        <div id="logo"><a href="<?php echo $homepage; ?>"><img src="resources/<?php echo $logo; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>" /></a></div>
    <?php } else if ( !empty($logo) ) { ?>
        <div id="logo"><a href="<?php echo $homepage; ?>"><?php echo $logo; ?></a></div>
    <?php } ?>
    <!-- header blocks placeholder -->
    <?php foreach ($children_blocks as $k => $block) { ?>
      <?php if ($k == count($children_blocks)-1 ) { ?>
      <div class="header_block flt_right"><?php echo ${$block}; ?></div>
      <?php } else { ?>
      <div class="header_block flt_left"><?php echo ${$block}; ?></div>
      <?php } ?>
    <?php } ?>
    <!-- header blocks placeholder (EOF) -->
    <div class="clr_both"></div>
  </div>
  <div class="h_section2">
	<div class="h_section2_left"><div class="h_section2_right"><div class="h_section2_mid"><?php echo  $menu; ?></div></div></div>
  </div>
  <div class="h_section3">
    <div class="h_section3_left"><div class="h_section3_right"><div class="h_section3_mid">
      <div class="t_block flt_left">
        <span id="external_links">
	        <?php
	        if($external_links){
		        foreach($external_links as $link){
			?>
			      <div><?php echo  $link; ?></div>
			<?php
		        }
	        }
	        ?>
        </span>
      </div>
      <div class="t_block flt_left">
        <ul id="info_links">
          <li><a href="<?php echo $special; ?>" class="special"><?php echo $text_special; ?></a></li>
          <li class="nav_sep">&nbsp;</li>
          <li><a onclick="bookmark(document.location, '<?php echo addslashes($title); ?>');" class="bookmark"><?php echo $text_bookmark; ?></a></li>
          <li class="nav_sep">&nbsp;</li>
          <li><a href="<?php echo $contact; ?>" class="contact"><?php echo $text_contact; ?></a></li>
          <li class="nav_sep">&nbsp;</li>
          <li><a href="<?php echo $sitemap; ?>" class="sitemap"><?php echo $text_sitemap; ?></a></li>
        </ul>
      </div>
      <div class="t_block flt_right">
        <span id="search"><?php echo $search; ?><a onclick="goSearch();" class="button_search">&nbsp;</a></span>
      </div>
    </div></div></div>
  </div>

 <?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php if ( !empty($breadcrumb['separator']) ) echo '<span class="breadcrumb-separator"><img src="'.$this->templateResource('/image/icon_breadcrumbs_more.gif').'" alt="" /></span>' ?>
    <span class="breadcrumb-element"><a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo ( $breadcrumb['text'] == $text_home ? '<img src="'.$this->templateResource('/image/icon_breadcrumbs_home.gif').'" alt="'.$text_home.'" />' : $breadcrumb['text'] ); ?>
    </a></span>
    <?php } ?>
  </div>
  <?php } ?>