<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-frown"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel text-center">

	<?php echo $text_error; ?>
	
</div>

<div class="container-fluid cart_total">

	<div class="col-md-6 mt20 pull-left">
	    <button onclick="goBack()" class="btn btn-default" title="<?php echo $button_back; ?>">
	    	<i class="fa fa-arrow-left"></i>
	    	<?php echo $button_back ?>
	    </button>
	</div>

</div>

<script>
function goBack() {
    window.history.back();
}
</script>

<?php echo $footer; ?>