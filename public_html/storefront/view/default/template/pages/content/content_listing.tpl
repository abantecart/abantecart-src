<div class="d-flex flex-nowrap sorting well bg-secondary bg-opacity-10 border mb-3">
    <form class="form-inline d-flex text-nowrap p-2 align-items-center">
        <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
    </form>
    <?php if($selected_tag){ ?>
    <div class="w-auto p-3">
        <a class="btn btn-outline-secondary btn-sm text-nowrap" href="<?php echo $remove_tag ?>">
            <?php echo $selected_tag ?> <i class="fa fa-close"></i>
        </a>
    </div>
    <?php } ?>
    <div class="btn-group ms-auto">
        <button class="btn btn-light border-dark" disabled><i class="fa fa-th-list"></i></button>
    </div>
</div>

<div id="content_list">
    <?php if($children){ ?>
        <div class="px-0 mx-0 container-fluid">
            <?php
            foreach ($children as $cld) {
            ?>
                <div class="card content-list-card mb-2">
                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center">
                            <?php if ($cld['icon_url']) { ?>
                                <div class="w-auto p-3">
                                    <a href="<?php echo $cld['url'] ?>">
                                        <img src="<?php echo $cld['icon_url'] ?>" alt="<?php echo_html2view($cld['title']); ?>" class="img-fluid" style="max-width: 200px; max-height: 200px">
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="w-100">
                                <?php if ($cld['new']) { ?>
                                    <div class="badge prod-badge bg-warning text-white"><span><?php echo $text_new ?></span></div>
                                <?php } ?>
                                <h6 class="my-3">
                                    <a class="text-decoration-none card-title" href="<?php echo $cld['url'] ?>">
                                    <?php echo $cld['title']; ?>
                                    </a>
                                </h6>
                                <p class="mb-0"><?php echo $cld['description'] ?></p>
                                <?php echo $this->getHookvar('content_listing_'.$cld['content_id']);?>
                                <ul class="list-inline mt-2 mb-0">
                                    <?php foreach ($cld['tags'] as $tag => $tag_url) { ?>
                                    <li class="list-inline-item">
                                        <a class="text-decoration-none" href="<?php echo $tag_url ?>">
                                            <i class="fa fa-tags fa-fw"></i>
                                            <?php echo $tag ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="w-auto">
                                <a class="btn btn-outline-secondary btn-sm text-nowrap" href="<?php echo $cld['url'] ?>">
                                    <i class="fa fa-eye"></i> <?php echo $button_view ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    <?php } ?>
</div>

<div class="w-100 mt-3 sorting well">
    <?php echo $pagination_bootstrap; ?>
</div>
<script type="text/javascript">
	$('#sort').change(function () {
		ResortContent('<?php echo $resort_url; ?>');
	});

	function ResortContent(url) {
		url += '&sort=' + $('#sort').val();
		url += '&limit=' + $('#limit').val();
		location = url;
	}
</script>