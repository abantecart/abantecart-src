<?php /* Footer */ ?>
<!-- footer bottom starts -->
        <div class="container">
                <div class="row">
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
            <div class="row mt-4 bottom-footer-block">
                <div class="col-lg-8">
                    <ul class="list-inline">
                        <li class="list-inline-item">
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
                    <div class="w-100 d-flex text-white text-opacity-75 my-4 flex-nowrap"><?php echo $text_project_label ?></div>
                    <p class="left text-white text-opacity-75 mb-3"> <?php echo $text_copy; ?></p>
                </div>
            </div>
        </div>
<!-- footer bottom ends -->


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
