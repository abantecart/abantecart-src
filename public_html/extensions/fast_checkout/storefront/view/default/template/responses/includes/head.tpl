<!DOCTYPE html>
<head>
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="-1">
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 11:00:00 GMT">
    <meta http-equiv="pragma" content="no-cache">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <base href="<?php echo $base; ?>"/>

    <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>"/>
    <?php } ?>

    <link href="<?php echo $this->templateResource('/css/font-awesome.min.css'); ?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo $this->templateResource('/css/bootstrap.min.css'); ?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo $this->templateResource('/css/bootstrap-xxs.css'); ?>" rel="stylesheet" type='text/css'/>
    <link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>

    <?php foreach ($styles as $style) { ?>
        <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>"
              media="<?php echo $style['media']; ?>"/>
    <?php } ?>

    <script type="text/javascript"
            src="<?php echo $ssl ? 'https' : 'http' ?>://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        if (typeof jQuery == 'undefined') {
            var include = '\x3Cscript type="text/javascript" src="<?php echo $this->templateResource("/javascript/jquery-1.11.0.min.js"); ?>">\x3C/script>';
            document.write(include);
        }
    </script>
    <script type="text/javascript"
            src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript"
            src="<?php echo $this->templateResource('/js/credit_card_validation.js'); ?>"></script>
    <script type="text/javascript"
            src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>

    <?php foreach ($scripts as $script) { ?>
        <script type="text/javascript" src="<?php echo $script; ?>"></script>
    <?php } ?>

</head>
<?php echo $header; ?>
<body>
<?php if ($maintenance_warning){ ?>
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $maintenance_warning; ?></strong>
</div>
<?php } ?>