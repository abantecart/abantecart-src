<?php /* Footer */ ?>
<footer class="bd-footer py-1 mt-5 bg-light">
    <div class="container py-1">
        <div class="row">
        <?php
            $shown = [];
            $count = count($children_blocks);
            $i = 1;
            foreach($children_blocks as $k => $block){
                if($i > ($count - 4)){ break; }
                echo '<div class="col-lg-3 mb-3">'. $$block .'</div>';
                $shown[] = $i;
                $i++;
            }
        ?>
        </div>
    </div>
    <nav class="navbar navbar-expand-xl navbar-dark bg-secondary text-light" role="navigation">
        <div class="container-fluid">
            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#footerNavbarCollapse"
                    aria-controls="footerNavbarCollapse"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="footerNavbarCollapse" class="navbar-collapse collapse">
                <div class="container d-flex flex-wrap justify-content-evenly navbar-nav mb-2 mb-md-0">
                    <?php
                    $i = 0;
                    foreach($children_blocks as $k => $instanceName){ 
                        $i++;
                        if(in_array($i, $shown)){ continue; }
                    ?>
                        <div class="align-self-center justify-content-center mx-sm-3 mb-3 block_<?php echo $k; ?>">
                            <?php echo $$instanceName; ?>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </nav>
</footer>


<div id="msgModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close callback-btn" data-dismiss="modal"
                        aria-hidden="true">&times;</button>
                <h3 class="hidden">&nbsp;</h3>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
