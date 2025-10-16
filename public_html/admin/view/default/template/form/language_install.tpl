<button class="btn btn-default tooltips" type="button" data-toggle="modal" data-target="#load_language_modal">
    <i class="fa fa-language"></i>
    <i class="fa fa-download"></i>
    <?php echo $text_load_languages; ?>
</button>
<?php
$modal_content = '<div class="add-lang-modal" >
			<div class="panel panel-default">
			    <div id="collapseTwo" >
			    	<div class="panel-body panel-body-nopadding">
			    	    <table class="table table-striped">';

            foreach($packages as $package){
                $modal_content .= '<tr>
                    <td><img src="'.$package['image_url'].'" alt="'.html2view($package['name']).'" style="width:20px"></td>
                    <td>'.$package['name'].'</td>
                    <td class="text-right">'
                .($package['install_url']
                    ? $this->html->buildElement(
                            [
                               'type' => 'button',
                               'style'=> 'btn btn-default install-language',
                               'href' => $package['install_url'],
                               'icon' => 'fa fa-download',
                               'text' => $this->language->get('text_install','extension/extensions')
                            ]
                    )
                    : '').'</td>
                </tr>';
            }
$modal_content .= '</table>
			    	</div>
			    	<div class="panel-footer">
			    		<div class="row">
			    		   <div class="center">
			    			 <button type="button" class="btn btn-default" data-dismiss="modal">
			    			 <i class="fa fa-times"></i> '.$this->language->get('text_close').'
			    			 </button>
			    		   </div>
			    		</div>
			    	</div>
			    </div>
			</div>
		</div>';

echo $this->html->buildElement(
    array(
          'type' => 'modal',
          'id' => 'load_language_modal',
          'modal_type' => 'sm',
          'title' => $text_load_languages,
          'content' => $modal_content)
    );
?>
<form id="installFrm" method="post" enctype="application/x-www-form-urlencoded" action="<?php echo $this->html->getSecureUrl('tool/package_installer/upload'); ?>">
    <input type="hidden" name="package_url" value="">
</form>
<script>
    $(document).ready(function(){
        $('.install-language').on('click',function(e){
            e.preventDefault();
            const url = $(this).attr('href');
            $('input[name="package_url"]').val(url);
            $('#installFrm').submit();
        });
    })

</script>
