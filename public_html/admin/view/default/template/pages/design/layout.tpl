<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php
$current_templ = '';
$templ_li_block = '';
foreach($templates as $t) {
	$ext_class = '';
    if ($tmpl_id == $t) {
    	$current_templ = $t;
    	$ext_class = ' class="disabled"';
	}
    $templ_li_block .= '<li'.$ext_class.'><a href="'. $page_url . '&tmpl_id='.$t.'">'.$t.'</a></li>';    
}
?>

<?php 
$current_page = '';
$current_ok_delete = '';
$page_li_block = '';
foreach($pages as $pg) {
	if(!empty($pg['name'])) { 
    	$uri = '&tmpl_id='.$tmpl_id.'&page_id='.$pg['page_id'].'&layout_id='.$pg['layout_id'];

		$ext_class = '';
		if( $pg['page_id'] == $page['page_id'] && $pg['layout_id'] == $page['layout_id'] ) { 
			$current_page = $pg['name'];
			$ext_class = ' class="disabled"';
		}
		$page_li_block .= '<li'.$ext_class.'><a href="'.$page_url.$uri.'" title="'. $pg['name'].'">'.$pg['layout_name'].'</a>';
		if(empty($pg['restricted'])) {
			$page_li_block .= '<a data-delete-url="'.$page_delete_url.$uri.'" class="delete_page_layout close"><i class="fa fa-trash-o"></i></a>';
			if($current_page) {
				$current_ok_delete = true;
			}
		}
		$page_li_block .= '</li>';
	}
}		
?>

<div class="row">
<div class="col-sm-12 col-lg-12">
<ul class="content-nav">
	<li>
		<?php echo $text_select_template; ?>
		<div class="btn-group">
		  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
		  	<i class="fa fa-folder-o"></i>
		    <?php echo $current_templ; ?> <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
		  	<?php echo $templ_li_block; ?>
		  </ul>
		</div>
	</li>
	<li>
		<div class="btn-group">
		  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
		  	<i class="fa fa-square-o"></i>
		    <?php echo $current_page; ?> <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
		  	<?php echo $page_li_block; ?>
		  </ul>
		</div>
	</li>
	<li>
	  <a class="itemopt" href=""><i class="fa fa-refresh"></i> Reload</a>
	</li>
	<li>
	  <a class="itemopt" href=""><i class="fa fa-save"></i> Save</a>
	</li>
	<?php if ($current_ok_delete) { ?>
	<li>
	  <a class="itemopt" href=""><i class="fa fa-trash-o"></i> Delete</a>
	</li>
	<?php }?>
</ul>
</div>
</div>

<div class="row">
<div class="col-sm-12 col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">

		<div id="page_layout">
		<?php echo $layoutform; ?>
		</div>

  		</div>
  	</div>
</div>
</div>

<script type="text/javascript"><!--
    
    $('.delete_page_layout').click(function() {
    	if ( confirm('<?php echo $text_delete_confirm; ?>' )) {
    		var url = $(this).attr('data-delete-url');
    		window.location = url + '&confirmed_delete=yes';	
    	}
    });
    
--></script>