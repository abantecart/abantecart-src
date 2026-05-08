<?php /* Footer */ ?>
<!-- footer bottom starts -->
        <div class="container">
                <div class="row clearfix">
                <?php
                    $shown = [];
                    $count = count($children_blocks);
                    $i = 1;
                    foreach($children_blocks as $k => $block){
                        if($i > ($count - 1)){ break; }
                        if($i == 2){
                            echo '<div class="col-md-6">'. $$block .'</div>';
                        }else{
                            echo '<div class="col-md-3">'. $$block .'</div>';
                        }
                        $shown[] = $i;
                        $i++;
                    }
                ?>
                </div> 
            <div class="row mt-4 bottom-footer-block clearfix">
                <ul class="col-lg-12 list-inline d-block d-md-inline-block">
                    <li class="list-md-inline-item">
                        <?php
                        $i = 0;
                        foreach($children_blocks as $k => $instanceName){
                            $i++;
                            if(in_array($i, $shown)){ continue; }
                        ?>
                            <div class="block_<?php echo $k; ?>">
                                <?php echo $$instanceName; ?>
                            </div>
                        <?php
                        } ?>
                    </li>
                </ul>
            </div>
        </div>
        <div class="container pb-0 mb-0">
            <div class="w-100 d-flex text-white text-opacity-50 py-4 justify-content-between footer-notes">
                <div class="d-flex flex-nowrap"><?php echo $text_copy; ?></div>
                <div class="d-flex flex-nowrap"><?php echo $text_project_label ?></div>
            </div>
        </div>
<!-- footer bottom ends -->

<div id="msgModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close callback-btn" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="hidden">&nbsp;</h3>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
