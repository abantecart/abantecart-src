<?php if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body>
<?php echo ${$header}; ?>

<div id="maincontainer">

<?php if ( !empty( ${$header_bottom} ) ) { ?>
<!-- header_bottom blocks placeholder -->
	<div class="row-fluid">
		<div class="span12">
	    <?php echo ${$header_bottom}; ?>
	  	</div>
	</div>
<!-- header_bottom blocks placeholder -->
<?php } ?>
  
<?php
	//check layout dynamicaly
	$present_columns = 1;
	$center_padding = '';
	if ( !empty(${$column_left}) ) {
		$present_columns++;
		$center_padding .= 'ct_padding_left';
	}
	if ( !empty(${$column_right}) ) {
		$present_columns++;
		$center_padding .= ' ct_padding_right';
	}
?>  
  
	<div class="container">
		<div class="row">
			<?php if ( !empty(${$column_left} ) ) { ?>
			<div class="span3 mt20">	
			<?php echo ${$column_left}; ?>
			</div>
			<?php } ?>
			
			<?php $span = 12 - 3 * ($present_columns -1); ?>
			<div class="span<?php echo $span ?> mt20">	
			<?php if ( !empty( ${$content_top} ) ) { ?>
			<!-- content top blocks placeholder -->
			<?php echo ${$content_top}; ?>
			<!-- content top blocks placeholder (EOF) -->
			<?php } ?>
			
			<div class="container-fluid <?php echo $center_padding; ?>">
			<?php echo $content; ?>
			</div>
			 
			<?php if ( !empty( ${$content_bottom} ) ) { ?>
			<!-- content bottom blocks placeholder -->
			<?php echo ${$content_bottom}; ?>
			<!-- content bottom blocks placeholder (EOF) -->
			<?php } ?>
			</div>	
			
			<?php if ( !empty(${$column_right} ) ) { ?>
			<div class="span3 mt20">	
			<?php echo ${$column_right}; ?>
			</div>
			<?php } ?>
		</div> <!-- row-->
	</div> <!-- content conteiner -->

</div>
<!-- /maincontainer -->

<?php if ( !empty( ${$footer_top} ) ) { ?>
<!-- footer top blocks placeholder -->
	<div class="row-fluid">
		<div class="span12">
	    <?php echo ${$footer_top}; ?>
	  	</div>
	</div>
<!-- footer top blocks placeholder -->
<?php } ?>

<!-- footer blocks placeholder -->
<div id="footer"><?php echo ${$footer}; ?></div>

<p style="position: absolute !important; text-indent: -10000px !important; font-size:1px !important; margin:0; padding:0; height:1px;" >
    <a style="margin:0; padding:0; height:1px; font-size:1px !important; margin:0; padding:0;" href="<?php echo $rnk_lnk;?>" title="<?php echo $rnk_text;?>" ><?php echo $rnk_text;?></a>
</p>
</body></html>