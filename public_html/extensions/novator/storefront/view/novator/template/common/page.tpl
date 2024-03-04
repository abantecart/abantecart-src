<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">
<?php echo $this->getHookVar('top_page'); ?>

<!-- header section Starts -->
<header>
    <div class="nav-wrapper">
        <!-- Main header section Starts -->
            <?php echo $$header; ?>
        <!-- Main header section Ends -->

        <a href="javascript:void(0)" class="theme-change" id="theme-switcher"><i class="bi bi-sun-fill icon-dark"></i> <i class="bi bi-moon-stars-fill icon-light"></i> </a>

        <!-- header-bottom-section Starts -->
            <div class="header-bottom-section">
                <?php if ( !empty( $$header_bottom ) ) { ?> 
                    <?php echo $$header_bottom; ?>        
                <?php } ?>
            </div>
        <!-- header-bottom-section Ends -->
    </div>
</header>
<!-- header section Ends -->

<div id="maincontainer" class="top-section mb-3">

    <?php
        //check layout dynamically
        $present_columns = 0;
        $center_padding = '';
        if (!empty($$column_left)) {
            $present_columns++;
            $center_padding .= ' ms-1 ';
        }
        if (!empty($$column_right)) {
            $present_columns++;
            $center_padding .= ' me-1 ';
        }
    ?>
    <div class="container">


    <div class="row align-items-stretch align-self-stretch justify-content-center">		
        
        <?php if ( !empty($$column_left ) ) { ?>
            <!-- column_left-section Starts -->
                <div class="column_left-section col-12 col-xl-3">
                    <?php echo $$column_left; ?>
                </div>
            <!-- column_left-section Ends (EOF) -->
        <?php } ?> 
        

        <?php $span = 12 - 6 * $present_columns; ?>

        <div class="page-main-content flex-grow-1 col-12 col-lg-<?php echo $span ?>">
            
            <?php if ( !empty( $$content_top ) ) { ?>  
                <!-- content-top-section Starts -->
                <?php echo $$content_top; ?>
                <!-- content-top-section Ends (EOF) -->
            <?php } ?>
            

            <!-- content-main-section Starts -->
                <div class="content-main-section">
                    
                        <?php echo $content; ?>
                    
                </div>
            <!-- content-main-section Ends (EOF) -->

            
            <?php if ( !empty( $$content_bottom ) ) { ?>      
                <!-- content-bottom-section Starts -->
                    <div class="content-bottom-section">                         
                            <?php echo $$content_bottom; ?>   
                    </div> 
                <!-- content-bottom-section Ends (EOF) -->            
            <?php } ?>
            
        </div>

        
        <?php if ( !empty($$column_right ) ) { ?>
            <!-- column_right-section Starts -->
                <div class="column_right-section col-12 col-lg-3">
                    <?php echo $$column_right; ?>
                </div>
            <!-- column_right-section Ends (EOF) -->
        <?php } ?>
        
    </div>
    
    </div>

</div>

<!-- footer section Starts -->
    <footer class="footer mt-5">    
        <!-- footer top section Starts -->
            <div class="footer-top">
                <?php if ( !empty( $$footer_top ) ) { ?>
                    <?php echo $$footer_top; ?>
                <?php } ?>
            </div>
        <!-- footer top section Ends -->

        <!-- footer bottom section Starts -->
            <div class="footer-bottom">
                <?php echo $$footer; ?>
            </div>
        <!-- footer bottom section Ends -->
    </footer>
<!-- footer section Ends -->

<!--
Commented by TM
</div>
-->


<a id="gotop" class="fs-6 go-top" href="#" title="<?php echo_html2view($this->language->get('text_back_on_top'));?>"><i class="fa-solid fa-circle-chevron-up fa-3x"></i></a>
<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate http://www.abantecart.com/donate
//-->
<?php
if($scripts_bottom && is_array($scripts_bottom)) {
	foreach ($scripts_bottom as $script){ ?>
        <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
    <?php }
} ?>
    <script type="text/javascript">
    $('.theme-change').on('click', function () {
        if ($("html").attr("data-bs-theme")) {
            $("html").removeAttr("data-bs-theme", "dark");
        } else {
            $("html").attr("data-bs-theme", "dark");
        }
    });
    </script>

    </body>
</html>