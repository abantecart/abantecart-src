<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">
<?php echo $this->getHookVar('top_page'); ?>

<!-- 
    Commented by TM
    <div class="main-container"  style="max-width: <?php echo $layout_width; ?>">
-->

<!-- header section Starts -->
<header>
    <div class="nav-wrapper">
        <?php if($maintenance_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-0">
                <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
                <b><?php echo $maintenance_warning;?></b>
                <?php if($act_on_behalf_warning){ ?>
                <b><?php echo $act_on_behalf_warning;?></b>
                <?php } ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        }

        if($act_on_behalf_warning && !$maintenance_warning){ ?>
            <div class="alert alert-warning alert-dismissible mb-0">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <b><?php echo $act_on_behalf_warning;?></b>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        ?>


        <!-- Main header section Starts -->
            <?php echo $$header; ?>
        <!-- Main header section Ends -->

        <a class="theme-change"><i class="bi bi-sun-fill icon-dark"></i> <i class="bi bi-moon-stars-fill icon-light"></i> </a>

        <!-- header-bottom-section Starts -->
            <div class="header-bottom-section">
                <?php if ( !empty( $$header_bottom ) ) { ?> 
                    <?php echo $$header_bottom; ?>        
                <?php } ?>
            </div>
        <!-- header-bottom-section Ends -->
    </div>

    <!-- TM Static content start -->
    <div class="offcanvas search-offcanvas offcanvas-end" tabindex="-1" id="searchoffcanvas" aria-labelledby="cartoffcanvasLabel">
        <div class="offcanvas-header text-end"><button type="button" class="btn btn-danger btn-icon ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x"></i></button></div><div class="offcanvas-body"><div class="btn-group"><button class="btn dropdown-toggle arrow-none bg-transparent shadow-none border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button><ul class="dropdown-menu"><li><a class="dropdown-item" href="#">Men</a></li><li><a class="dropdown-item" href="#">Female</a></li><li><a class="dropdown-item" href="#">Fragrance</a></li><li><a class="dropdown-item" href="#">Makeup</a></li><li><a class="dropdown-item" href="#">Skincare</a></li><li><a class="dropdown-item" href="#">Perfume</a></li></ul></div><div class="input-group"><input type="text" class="form-control bg-transparent shadow-none border-0" placeholder="What are you searching for?"> <a class="btn-search" aria-label="Search" href="#" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></a></div><div class="d-grid"><button class="btn btn-primary">Serach</button></div></div>
    </div>

    <div class="offcanvas menu-offcanvas offcanvas-end" tabindex="-1" id="menuoffcanvas" aria-labelledby="cartoffcanvasLabel">
        <div class="offcanvas-body"><div class="menu-top-btn">
        <button type="button" class="btn btn-danger btn-icon" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x"></i></button> <button class="btn btn-primary">Menu</button> 
        <button class="btn btn-primary" data-bs-toggle="offcanvas"><i class="bi bi-handbag"></i> Shop</button> 
        <button class="btn btn-primary"><i class="bi bi-person"></i> Account</button></div></div><div class="scroll-div">
        <div class="offcanvas-body"><div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mob-menu-collapseOne" aria-expanded="false" aria-controls="mob-menu-collapseOne">Home</button></h2><div id="mob-menu-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body px-0"><div class="list-group list-group-flush"><a href="#" class="list-group-item list-group-item-action border-0">Shipping Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of Use</a></div></div></div></div><div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mob-menu-collapseTwo" aria-expanded="false" aria-controls="mob-menu-collapseTwo">Pages</button></h2><div id="mob-menu-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body px-0"><div class="list-group list-group-flush"><a href="#" class="list-group-item list-group-item-action border-0">Shipping Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of Use</a></div></div></div></div><div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mob-menu-collapseThree" aria-expanded="false" aria-controls="mob-menu-collapseThree">Offer</button></h2><div id="mob-menu-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"><div class="accordion-body px-0"><div class="list-group list-group-flush"><a href="#" class="list-group-item list-group-item-action border-0">Shipping Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of Use</a></div></div></div></div><div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed no-icon" type="button">My Orders</button></h2></div></div></div><div class="offcanvas-body border-top border-bottom"><h5 class="ms-4">General Information</h5><div class="list-group list-group-flush mt-4"><a href="#" class="list-group-item list-group-item-action border-0">Shipping Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of Use</a></div></div><div class="offcanvas-body"><div class="btn-group"><button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">ENG <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button><ul class="dropdown-menu"><li><a class="dropdown-item" href="#">HINDI</a></li><li><a class="dropdown-item" href="#">FRENCH</a></li><li><a class="dropdown-item" href="#">SPENISH</a></li><li><a class="dropdown-item" href="#">URDU</a></li></ul></div><div class="btn-group ms-2"><button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">$ USD <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button><ul class="dropdown-menu"><li><a class="dropdown-item" href="#">RUPEES</a></li><li><a class="dropdown-item" href="#">EURO</a></li><li><a class="dropdown-item" href="#">POUND</a></li><li><a class="dropdown-item" href="#">DIRHAM</a></li></ul></div></div></div>
    </div>

    <div class="offcanvas card-offcanvas offcanvas-end" tabindex="-1" id="cartoffcanvas" aria-labelledby="cartoffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <div class="row w-100 align-items-center justify-content-between">
                <div class="col-auto">
                <h5 class="mb-0">Shopping cart (2)</h5>
                </div>
                <div class="col-auto"><a href="#" class="link-secondary">Clear All</a> <button type="button" class="btn-close"
                    data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
            </div>
        </div>
        <div class="offcanvas-body">
        <div class="alert alert-dismissible fade show pe-3" role="alert">
            <div class="d-flex">
            <div class="flex-shrink-0"><img src="https://template.yourwebstore.net/135oct/image/thumbnails/18/6a/demo_product18_jpg-100013-312x400.jpg" alt="image" class="img-fluid h-auto"
                width="90" height="90"></div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <p class="mb-0">SportSware Dri-FIT Rise</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="btn-group btn-group-sm mb-0" role="group" aria-label="button groups sm">
                        <button type="button"
                        id="decrease" onclick="decreaseValue('number')" class="btn border-0 shadow-none">-</button> <input
                        class="text-center border-0 shadow-none" type="text" id="number" value="0"> <button
                        type="button" id="increase" onclick="increaseValue('number')"
                        class="btn border-0 shadow-none">+</button>
                    </div>
                    <h5 class="mb-0">$599</h5>
                </div>
            </div>
            </div>
        </div>
        <div class="alert alert-dismissible fade show pe-3" role="alert">
            <div class="d-flex">
            <div class="flex-shrink-0"><img src="https://template.yourwebstore.net/135oct/image/thumbnails/18/6a/demo_product18_jpg-100013-312x400.jpg" alt="image" class="img-fluid h-auto"
                width="90" height="90"></div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="mb-0">SportSware Dri-FIT Rise</p><button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="btn-group btn-group-sm mb-0" role="group" aria-label="button groups sm"><button type="button"
                    id="decrease" onclick="decreaseValue('number')" class="btn border-0 shadow-none">-</button> <input
                    class="text-center border-0 shadow-none" type="text" id="number" value="0"> <button
                    type="button" id="increase" onclick="increaseValue('number')"
                    class="btn border-0 shadow-none">+</button></div>
                <h5 class="mb-0">$599</h5>
                </div>
            </div>
            </div>
        </div>
        <div class="alert alert-dismissible fade show pe-3" role="alert">
            <div class="d-flex">
            <div class="flex-shrink-0"><img src="https://template.yourwebstore.net/135oct/image/thumbnails/18/6a/demo_product18_jpg-100013-312x400.jpg" alt="image" class="img-fluid h-auto"
                width="90" height="90"></div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="mb-0">SportSware Dri-FIT Rise</p><button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="btn-group btn-group-sm mb-0" role="group" aria-label="button groups sm"><button type="button"
                    id="decrease" onclick="decreaseValue('number')" class="btn border-0 shadow-none">-</button> <input
                    class="text-center border-0 shadow-none" type="text" id="number" value="0"> <button
                    type="button" id="increase" onclick="increaseValue('number')"
                    class="btn border-0 shadow-none">+</button></div>
                <h5 class="mb-0">$599</h5>
                </div>
            </div>
            </div>
        </div>
        </div>
        <div class="offcanvas-body border-top mt-4 pt-4">
        <div class="row w-100 align-items-center justify-content-between mb-3">
            <div class="col-auto">
            <h5 class="mb-0">subtotal:</h5>
            </div>
            <div class="col-auto">
            <h5 class="mb-0 text-warning">$1,547</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
            <div class="d-grid"><button class="btn btn-dark">VIEW CART</button></div>
            </div>
            <div class="col-6">
            <div class="d-grid"><button class="btn btn-warning">CHECKOUT</button></div>
            </div>
        </div>
        </div>
    </div>
    <!-- TM Static content end -->

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
                    <div class="content-top-section">                  
                        <?php echo $$content_top; ?>
                    </div>
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