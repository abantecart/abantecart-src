<header>
    <div class="headerstrip navbar" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header header-logo">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target=".navbar-collapse">
                    <span class="sr-only"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php if (is_file(DIR_RESOURCE.$logo)) { ?>
                    <a class="logo" href="<?php echo $homepage; ?>">
                        <img src="resources/<?php echo $logo; ?>" width="<?php echo $logo_width; ?>"
                             height="<?php echo $logo_height; ?>" title="<?php echo $store; ?>"
                             alt="<?php echo $store; ?>"/>
                    </a>
                <?php } else {
                    if (!empty($logo)) { ?>
                        <a class="logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
                    <?php }
                } ?>
            </div>
        </div>
</header>