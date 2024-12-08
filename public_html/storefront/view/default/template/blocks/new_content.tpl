<?php if($contents){ ?>
<div class="px-3 container-fluid container-xl">
    <h4 class="h4 heading-title"><?php echo $heading_title; ?></h4>
    <?php echo $this->getHookVar('pre_contents'); ?>
<?php
foreach ($contents as $content) {
    ?>
    <div class="d-flex flex-wrap p-2 border-bottom border-1">
        <?php if ($content['icon_url']) { ?>
        <div class="w-100 p-2">
            <a href="<?php echo $content['url'] ?>">
                <img class="mx-auto d-block" src="<?php echo $content['icon_url'] ?>">
            </a>
            <?php echo $this->getHookvar('content_listing_icon_'.$content['content_id']);?>
        </div>
        <?php } elseif ($content['icon_code']) { ?>
            <div class="w-auto p-2">
                <?php echo $content['icon_url'] ?>
                <?php echo $this->getHookvar('content_listing_icon_'.$content['content_id']);?>
            </div>
        <?php } ?>
        <div class="w-100">
            <a class="text-decoration-none text-secondary card-title" href="<?php echo $content['url'] ?>">
                <h5><?php echo $content['title']; ?></h5>
            </a>
            <div class="blurb"><?php echo $content['description'] ?></div>
            <?php echo $this->getHookvar('content_listing_descr_'.$content['content_id']);?>
        </div>
    </div>
    <?php
    }
    ?>
</div>
<?php } ?>