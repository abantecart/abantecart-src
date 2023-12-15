<?php if ($breadcrumbs && sizeof($breadcrumbs) > 1) { ?>
<section class="pb-0 pt-3">
    <div class="container">
        <nav
            aria-label="breadcrumb"
            style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);">
        
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
                            if($key != $lastKey){    ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ol>
        </nav>
    </div>
</section>
<?php } ?>