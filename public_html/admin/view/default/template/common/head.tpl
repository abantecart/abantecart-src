<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>"/>
<meta http-equiv="x-ua-compatible" content="IE=8"/>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>"/>
<?php } ?>

<?php if (is_file(DIR_RESOURCE . $icon)) { ?>
<link href="<?php echo HTTP_DIR_RESOURCE . $icon; ?>" type="image/png" rel="icon"/>
<?php } else if (!empty($icon)) { ?>
<?php echo $icon; ?>
<?php } ?>

<link rel="stylesheet" type="text/css"
      href="<?php echo $template_dir; ?>javascript/jquery/ui/themes/ui-lightness/ui.all.css"/>
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>"
      media="<?php echo $style['media']; ?>"/>
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/bootstrap.custom.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/stylesheet.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/abantecart.jquery.ui.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/font-awesome.min.css" media="all" />
<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/font-awesome-ie7.min.css" media="all" />
<![endif]-->

<script type="text/javascript"
        src="<?php echo $ssl ? 'https' : 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
    if (typeof jQuery == 'undefined') {
        var include = '<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery-1.7.2.min.js"><\/script>';
        document.write(include);
    }
</script>
<script type="text/javascript"
        src="<?php echo $template_dir; ?>javascript/jquery/ui/jquery-ui-1.8.22.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/superfish/js/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/superfish/js/superfish.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/tab.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery.number_format.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jqgrid/plugins/jquery.tablednd.js"></script>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/bootstrap-tooltip.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/bootstrap-popover.js"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
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
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/aform.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/general.js"></script>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/ckeditor/adapters/jquery.js"></script>
