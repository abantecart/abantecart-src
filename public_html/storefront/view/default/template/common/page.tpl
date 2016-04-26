<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body>
<div class="container-fixed" style="max-width: <?php echo $layout_width; ?>">

<?php if($maintenance_warning){ ?>
	<div class="alert alert-warning">
	 	<button type="button" class="close" data-dismiss="alert">&times;</button>
 		<strong><?php echo $maintenance_warning;?></strong>
 	</div>
<?php
}
echo ${$header}; ?>

<?php if ( !empty( ${$header_bottom} ) ) { ?>
<!-- header_bottom blocks placeholder -->
	<div class="container-fluid">
	    <?php echo ${$header_bottom}; ?>
	</div>
<!-- header_bottom blocks placeholder -->
<?php } ?>

<div id="maincontainer">

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

	<div class="container-fluid">
		<?php if ( !empty(${$column_left} ) ) { ?>
		<div class="column_left col-md-3 col-xs-12">
		<?php echo ${$column_left}; ?>
		</div>
		<?php } ?>

		<?php $span = 12 - 3 * ($present_columns -1); ?>
		<div class="col-md-<?php echo $span ?> col-xs-12 mt20">
		<?php if ( !empty( ${$content_top} ) ) { ?>
		<!-- content top blocks placeholder -->
		<?php echo ${$content_top}; ?>
		<!-- content top blocks placeholder (EOF) -->
		<?php } ?>
		
		<div class="<?php echo $center_padding; ?>">
		<?php echo $content; ?>
		</div>
		 
		<?php if ( !empty( ${$content_bottom} ) ) { ?>
		<!-- content bottom blocks placeholder -->
		<?php echo ${$content_bottom}; ?>
		<!-- content bottom blocks placeholder (EOF) -->
		<?php } ?>
		</div>

		<?php if ( !empty(${$column_right} ) ) { ?>
		<div class="column_right col-md-3 col-xs-12 mt20">
		<?php echo ${$column_right}; ?>
		</div>
		<?php } ?>
	</div>

</div>

<?php if ( !empty( ${$footer_top} ) ) { ?>
<!-- footer top blocks placeholder -->
	<div class="container-fluid">
		<div class="col-md-12">
	    <?php echo ${$footer_top}; ?>
	  	</div>
	</div>
<!-- footer top blocks placeholder -->
<?php } ?>

<!-- footer blocks placeholder -->
<div id="footer">
	<?php echo ${$footer}; ?>
</div>

</body></html>