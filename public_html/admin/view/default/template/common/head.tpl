<meta charset="utf-8">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>"/>
<?php } ?>

<?php if (is_file(DIR_RESOURCE . $icon)) { ?>
<link href="<?php echo HTTP_DIR_RESOURCE . $icon; ?>" type="image/png" rel="icon"/>
<?php } else if (!empty($icon)) { ?>
<?php echo $icon; ?>
<?php } ?>

<link rel="stylesheet" type="text/css" href="<?php echo $template_dir; ?>stylesheet/stylesheet.css" />

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>"
      media="<?php echo $style['media']; ?>"/>
<?php } ?>

<script type="text/javascript"
        src="<?php echo $ssl ? 'https' : 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
    if (typeof jQuery == 'undefined') {
        var include = '<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery-1.11.0.min.js"><\/script>';
        document.write(include);
    }
</script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/jquery-ui/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/bootstrap.min.js"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>

<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/aform.js"></script>

<?php 
	//Generic PHP processed Javascript section
?>
<script type="text/javascript">
$(document).ready(function () {
  
	numberSeparators = {decimal:'<?php echo $decimal_point; ?>', thousand:'<?php echo $thousand_point; ?>'};

});

var wrapConfirmDelete = function(){
    var wrapper = '<div class="btn-group dropup" />';
    var popover, href;

    $('a[data-confirmation="delete"]').each( function(){
        if($(this).attr('data-toggle')=='dropdown' ){ return;}
        
       	var action = $(this).attr('onclick');
        if ( action ) {
        	action = 'onclick="'+action+'"';
        } else {
	        href = $(this).attr('href');
    	    if(href.length==0 || href=='#'){ return;}
    	    action = 'href="' + href +'"';
        }
        
        $(this).wrap(wrapper);
        popover = '<div class="dropdown-menu dropdown-menu-right alert alert-danger" role="menu">'+
                    '<h4 class="center"><?php echo $text_confirm; ?></h4>'+
                    '<div class="center">'+
                    '<a class="btn btn-danger" '+action+' ><i class="fa fa-trash-o"></i> Yes</a>&nbsp;&nbsp;'+
                    '<a class="btn btn-default"><i class="fa fa-undo"></i> No</a>'+
                    '</div>'+
                    '</div>';
        $(this).after(popover);
        $(this).attr('onclick','return false;');
        $(this).attr('data-toggle','dropdown').addClass('dropdown-toggle');
    });
}

</script>
<?php 
	//NOTE: More JS loaded in page.tpl. This is to improve performance. Do not move above to page.tpl
?>