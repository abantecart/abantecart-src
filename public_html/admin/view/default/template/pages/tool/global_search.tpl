<?php
/** @see public_html/admin/view/default/template/common/action_confirm.tpl */
include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
            <?php
            /** @see public_html/admin/view/default/template/common/grid_search_form.tpl */
            include($tpl_common_dir . 'grid_search_form.tpl');?>
		</div>
        <?php
        /** @see public_html/admin/view/default/template/common/content_buttons.tpl */
        include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

<?php if ($search_categories) {?>
	<div class="panel-body-nopadding tab-content col-xs-12">
        <ul id="search_tabs" class="nav nav-tabs nav-justified nav-profile" role="tablist">
<?php   $i=0;
        foreach ($search_categories as $scat) {	?>
          <li class="disabled <?php echo $i==0 ? 'active' : ''; ?>" >
              <a id="tab_<?php echo $scat;?>_grid" href="#<?php echo $scat;?>" role="tab" data-toggle="tab">
                  <?php echo $search_categories_names[$scat];?>
              </a>
          </li>
    <?php  $i++;
        } ?>
        </ul>
    <div class="tab-content">
    <?php   $i=0;
            foreach ($search_categories as $scat) {	?>
                <div class="tab-pane" id="<?php echo $scat; ?>">
                    <div class="row">
                        <div class="col-sm-12 col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?php echo ${"listing_grid_" . $scat}; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $i++; } ?>
        </div>

	</div>
<?php } else { ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div class="flt_none clr_both heading"><?php echo $scat;?></div>
		<table class="table_list">
            <tr>
                <td class="left" id="no results"><?php echo $no_results_message; ?></td>
            </tr>
		</table>
	</div>
<?php } ?>

</div>

<?php
echo $this->html->buildElement(
		[
            'type'        => 'modal',
            'id'          => 'gs_modal',
            'modal_type'  => 'lg',
            'data_source' => 'ajax'
        ]
); ?>

<script type="text/javascript">
    let gridInits = <?php echo json_encode($grid_inits)?>;
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		const target = $(e.target).attr("href");
		$(target+'_grid').trigger( 'resize' );
	});

	// Javascript to enable link to tab
	let hash = document.location.hash.replace('#','');
	if (hash) {
	    $('#search_tabs a[href="#'+hash+'"]').tab('show') ;
	}

	// Change hash for page-reload
	$('.nav-tabs a').on('click', function (e) {
	    window.location.hash = e.target.hash;
        hash = window.location.hash;
    });

	let grid_ready = function (grid_id, data){
		if( grid_id === 'languages_grid' ){
			$('#'+grid_id)
                .find('td[aria-describedby$="_grid_search_result"]>a')
                    .each(
                        function () {
                            $(this).attr('data-toggle', 'modal').attr('data-target', '#gs_modal');
                        }
                    );
		}
        const li = $('#tab_'+grid_id).parent();
        if(data.records<1){
            li.remove();
        }else{
            li.removeClass('disabled');
            hash = hash ? hash : grid_id.replace('_grid','');
            if($('#search_tabs').find('li.active').length<1){
                li.find('a').tab('show') ;
            }
        }
	}

	$('span.icon_search').click(function(){
		$('#search_form').submit();
	});
<?php //call opened tab's grid and then call other grids with delay ?>
	$(document).ready(function(){
        if(gridInits[hash]) {
            setTimeout(gridInits[hash] + '($);', 100);
        }
        let time = 700;
        for(const i in gridInits){
            if(gridInits[i] === gridInits[hash]){
                continue;
            }
            setTimeout(gridInits[i]+'($);',time);
            time+=700;
        }
	});
</script>