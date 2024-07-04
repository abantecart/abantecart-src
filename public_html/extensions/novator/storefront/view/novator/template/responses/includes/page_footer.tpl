<?php /* Footer */ ?>
<div class="bd-footer py-0 <?php echo $children_blocks ? "mt-5 bg-light" : '' ?>">
    <nav class="m-0 navbar navbar-expand navbar-dark bg-secondary text-light" role="navigation">
        <div class="container-fluid p-3">
            <?php if($children_blocks){ ?>
            <div class="container d-flex flex-wrap justify-content-evenly text-center navbar-nav mb-2 mb-md-0">
                <?php
                $i = 0;
                foreach($children_blocks as $k => $instanceName){
                    $i++;
                    if(in_array($i, (array)$shown)){ continue; }
                ?>
                    <div class="align-self-center justify-content-center mx-sm-3 m-3 block_<?php echo $k; ?>">
                        <?php echo $$instanceName; ?>
                    </div>
                <?php
                } ?>
            </div>
            <?php } ?>
            <div class="container d-flex flex-wrap justify-content-evenly text-center navbar-nav mb-2 mb-md-0">
            <div class="project-label col-lg-3 my-3">
                <?php echo $text_copy; ?>
                <br>
                <?php echo $text_project_label ?>
            </div>
            </div>
        </div>
    </nav>
</div>


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