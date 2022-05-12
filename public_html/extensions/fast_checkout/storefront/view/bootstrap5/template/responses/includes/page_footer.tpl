<?php /* Footer */ ?>
<footer class="bd-footer py-1 mt-5 bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-secondary text-light" role="navigation">
        <div class="container-fluid">
            <div id="footerNavbarCollapse" class="navbar-collapse collapse">
                <div class="container d-flex flex-wrap justify-content-evenly text-center navbar-nav mb-2 mb-md-0">
                    <?php
                    $i = 0;
                    foreach($children_blocks as $k => $instanceName){
                        $i++;
                        if(in_array($i, (array)$shown)){ continue; }
                    ?>
                        <div class="align-self-center justify-content-center mx-sm-3 mb-3 block_<?php echo $k; ?>">
                            <?php echo $$instanceName; ?>
                        </div>
                    <?php
                    } ?>
                    <div class="project-label col-lg-3 my-3">
                        <?php echo $text_copy; ?>
                        <br>
                        <?php echo $text_project_label ?>
                    </div>
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