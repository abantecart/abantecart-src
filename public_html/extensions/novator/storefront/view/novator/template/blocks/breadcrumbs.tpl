<?php if ($breadcrumbs && sizeof($breadcrumbs) > 1) { ?>
<section class="pb-0 pt-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                    end($breadcrumbs);
                    $lastKey = key($breadcrumbs);
                    foreach($breadcrumbs as $key => $breadcrumb){ ?>
                    <li class="text-decoration-none breadcrumb-item <?php echo $key == $lastKey ? 'active" aria-current="page' : ''; ?>">
                    <?php if($key != $lastKey){ ?>
                        <a class="text-decoration-none" href="<?php echo $breadcrumb['href']; ?>">
                    <?php
                        }
                        echo ($breadcrumb['text'] == $text_home ? '<i class="bi bi-home me-1" title="' . $text_home . '"></i> ' : '');
                        echo $breadcrumb['text'];
                        if($key != $lastKey){ ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ol>
        </nav>
    </div>
</section>
<?php } ?>