<!DOCTYPE html>
<head>
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="-1">
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 11:00:00 GMT">
    <meta http-equiv="pragma" content="no-cache">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <base href="<?php echo $base; ?>"/>



    <link href="<?php echo $this->templateResource('/css/bootstrap.min.css'); ?>" rel="stylesheet" type='text/css' />
    <link href="<?php echo $this->templateResource('/fontawesome/css/all.min.css'); ?>" rel="stylesheet" type='text/css' />
    <link href="<?php echo $this->templateResource('/css/style.css'); ?>" rel="stylesheet" type='text/css' />
    <link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>

    <?php foreach ($styles as $style) { ?>
    <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>

    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>

    <script type="text/javascript" src="<?php echo $this->templateResource('/js/jquery-3.6.0.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $this->templateResource('/js/bootstrap.bundle.min.js'); ?>"></script>

    <script type="text/javascript" src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $this->templateResource('/js/credit_card_validation.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>

    <?php foreach ($scripts as $script) { ?>
        <script type="text/javascript" src="<?php echo $script; ?>"></script>
    <?php } ?>

</head>
<?php echo $header; ?>
<body>
<?php if ($maintenance_warning){ ?>
    <div class="alert alert-warning alert-dismissible">
        <i class="fa-solid fa-circle-exclamation me-2">
        <strong><?php echo $maintenance_warning;?></strong>
        <?php if($act_on_behalf_warning){ ?>
           <br/><strong><?php echo $act_on_behalf_warning;?></strong>
        <?php } ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>