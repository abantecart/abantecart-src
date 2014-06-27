<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $direction; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php echo $head; ?></head>
<body>

<!-- page loader -->
<div id="preloader">
    <div id="status"><i class="fa fa-spinner fa-spin"></i></div>
</div>
<!-- / page loader -->

<div class="wrapper">
  	<div class="wrapper_c page_width" style="width: <?php echo $layout_width; ?>">
		<section>

			<?php echo $header; ?>

			<div class="contentpanel">
			<?php echo $content; ?>
			</div><!-- / contentpanel -->

			<?php echo $footer; ?>

		</section>
	</div><!-- / Container wrapper -->
</div><!-- /Page wrapper -->

<?php echo $this->getHookVar('hk_page_footer'); ?>

<!--
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/browser.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jqgrid/plugins/jquery.tablednd.js"></script>
-->
<script type="text/javascript">
    //-----------------------------------------
    // Confirm Actions (delete, uninstall)
    //-----------------------------------------
    $(document).ready(function () {
        // Confirm Delete
        $('#form').submit(function () {
            if ($(this).attr('action').indexOf('delete', 1) != -1) {
                if (!confirm('<?php echo $text_confirm; ?>')) {
                    return false;
                }
            }
        });

        // Confirm Uninstall
        $('a').click(function () {
            if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
                if (!confirm('<?php echo $text_confirm; ?>')) {
                    return false;
                }
            }
        });
        numberSeparators = {decimal:'<?php echo $decimal_point; ?>', thousand:'<?php echo $thousand_point; ?>'};
    });

</script>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/modernizr.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/toggles.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/retina.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery.cookies.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/chosen.jquery.min.js"></script>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/general.js"></script>

</body></html>