<?php /* Footer */ ?>
<footer class="bd-footer pt-1 bg-light">
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
    <nav class="navbar navbar-dark bg-secondary text-light" role="navigation">
        <div class="w-100 d-flex flex-wrap justify-content-center mb-2 mb-md-0">
            <?php
            $i = 0;
            foreach($children_blocks as $k => $instanceName){
                $i++;
                if(in_array($i, $shown)){ continue; }
            ?>
                <div class="align-self-center justify-content-center mx-sm-3 my-3 block_<?php echo $k; ?>">
                    <?php echo $$instanceName; ?>
                </div>
            <?php
        } ?>
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
