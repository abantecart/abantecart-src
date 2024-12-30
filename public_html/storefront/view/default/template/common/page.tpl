<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">
<?php echo $this->getHookVar('top_page'); ?>

<div class="container-fixed" style="max-width: <?php echo $layout_width; ?>">

<?php if($maintenance_warning){ ?>
    <div class="alert alert-warning alert-dismissible mb-0">
        <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
        <b><?php echo $maintenance_warning;?></b>
        <?php if($act_on_behalf_warning){ ?>
           <b><?php echo $act_on_behalf_warning;?></b>
        <?php } ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
if($act_on_behalf_warning && !$maintenance_warning){ ?>
    <div class="alert alert-warning alert-dismissible mb-0">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <b><?php echo $act_on_behalf_warning;?></b>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }
echo $$header; ?>

<?php if ( !empty( $$header_bottom ) ) { ?>
<!-- header_bottom blocks placeholder -->
    <div class="container-fluid">
        <?php echo $$header_bottom; ?>
    </div>
<!-- header_bottom blocks placeholder -->
<?php } ?>

<div id="maincontainer" class="mt-3 mb-3">

<?php
    //check layout dynamically
    $present_columns = 0;
    $center_padding = '';
    if (!empty($$column_left)) {
        $present_columns++;
        $center_padding .= ' ms-1 ';
    }
    if (!empty($$column_right)) {
        $present_columns++;
        $center_padding .= ' me-1 ';
    }
?>

        <div class="d-flex flex-wrap align-items-stretch align-self-stretch justify-content-center m-3">
        <?php if ( !empty($$column_left ) ) { ?>
        <div class="col-sm-12 col-lg-3">
        <?php echo $$column_left; ?>
        </div>
        <?php } ?>

        <?php $span = 12 - 3 * $present_columns; ?>
            <div class="col-sm-12 col-lg-<?php echo $span ?>">
            <?php if ( !empty( $$content_top ) ) { ?>
            <!-- content top blocks placeholder -->
            <?php echo $$content_top; ?>
            <!-- content top blocks placeholder (EOF) -->
            <?php } ?>

            <div class="container-fluid">
            <?php echo $content; ?>
            </div>

            <?php if ( !empty( $$content_bottom ) ) { ?>
            <!-- content bottom blocks placeholder -->
            <?php echo $$content_bottom; ?>
            <!-- content bottom blocks placeholder (EOF) -->
            <?php } ?>
        </div>

        <?php if ( !empty($$column_right ) ) { ?>
            <div class="col-sm-12 col-lg-3">
        <?php echo $$column_right; ?>
        </div>
        <?php } ?>
    </div>

</div>

<?php if ( !empty( $$footer_top ) ) { ?>
<!-- footer top blocks placeholder -->
    <div class="d-flex w-100 justify-content-evenly flex-wrap px-0 mx-0 border">
        <div class="col-md-12">
        <?php echo $$footer_top; ?>
        </div>
    </div>
<!-- footer top blocks placeholder -->
<?php } ?>

<!-- footer blocks placeholder -->
<div id="footer">
    <?php echo $$footer; ?>
</div>
    <div class="mx-auto my-3 text-center">
        <?php echo $text_copy.'<span class="mx-4"></span>'.$text_project_label; ?>
    </div>
</div>
<a id="gotop" class="fs-6" href="#" title="<?php echo_html2view($this->language->get('text_back_on_top'));?>"><i class="fa-solid fa-circle-chevron-up fa-3x"></i></a>
<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate http://www.abantecart.com/donate
//-->
<?php
if($scripts_bottom && is_array($scripts_bottom)) {
    foreach ($scripts_bottom as $script){ ?>
        <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
    <?php }
} ?>
    </body>
</html>