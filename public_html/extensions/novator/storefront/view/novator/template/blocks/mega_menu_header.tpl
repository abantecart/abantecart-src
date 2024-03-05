<?php
$categories = prepareNVCatItems($categories);
?>
<?php // MOBILE HEADER MODE ?>
<div class="d-block d-lg-none">
   <a class="mob-nav-btn menu-icon bg-transparent text-primary" data-bs-toggle="offcanvas" href="#menuoffcanvas" role="button" aria-controls="menuoffcanvas"><i class="bi bi-justify"></i></a>
</div>

<div class="offcanvas menu-offcanvas offcanvas-end" tabindex="-1" id="menuoffcanvas"
     aria-labelledby="cartoffcanvasLabel">
    <div class="offcanvas-body">
        <div class="menu-top-btn">
            <button type="button" class="btn btn-danger btn-icon" data-bs-dismiss="offcanvas" aria-label="Close"><i
                        class="bi bi-x"></i></button>
            <button class="btn btn-primary">Menu</button>
            <button class="btn btn-primary" data-bs-toggle="offcanvas" href="#cartoffcanvas"><i
                        class="bi bi-handbag"></i> Shop
            </button>
            <button class="btn btn-primary"><i class="bi bi-person"></i> Account</button>
        </div>
    </div>
    <div class="scroll-div">
        <div class="offcanvas-body">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#mob-menu-collapseOne" aria-expanded="false"
                                aria-controls="mob-menu-collapseOne">Home
                        </button>
                    </h2>
                    <div id="mob-menu-collapseOne" class="accordion-collapse collapse"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body px-0">
                            <div class="list-group list-group-flush"><a href="#"
                                                                        class="list-group-item list-group-item-action border-0">Shipping
                                    Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy
                                    Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of
                                    Use</a></div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#mob-menu-collapseTwo" aria-expanded="false"
                                aria-controls="mob-menu-collapseTwo">Pages
                        </button>
                    </h2>
                    <div id="mob-menu-collapseTwo" class="accordion-collapse collapse"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body px-0">
                            <div class="list-group list-group-flush"><a href="#"
                                                                        class="list-group-item list-group-item-action border-0">Shipping
                                    Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy
                                    Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of
                                    Use</a></div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#mob-menu-collapseThree" aria-expanded="false"
                                aria-controls="mob-menu-collapseThree">Offer
                        </button>
                    </h2>
                    <div id="mob-menu-collapseThree" class="accordion-collapse collapse"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body px-0">
                            <div class="list-group list-group-flush"><a href="#"
                                                                        class="list-group-item list-group-item-action border-0">Shipping
                                    Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy
                                    Policy</a> <a href="#" class="list-group-item list-group-item-action border-0">Terms of
                                    Use</a></div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed no-icon" type="button">My Orders</button>
                    </h2>
                </div>
            </div>
        </div>
        <div class="offcanvas-body border-top border-bottom"><h5 class="ms-4">General Information</h5>
            <div class="list-group list-group-flush mt-4"><a href="#"
                                                             class="list-group-item list-group-item-action border-0">Shipping
                    Info</a> <a href="#" class="list-group-item list-group-item-action border-0">Privacy Policy</a> <a
                        href="#" class="list-group-item list-group-item-action border-0">Terms of Use</a></div>
        </div>
        <div class="offcanvas-body">
            <div class="btn-group">
                <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">ENG <i data-feather="chevron-down"></i></button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">HINDI</a></li>
                    <li><a class="dropdown-item" href="#">FRENCH</a></li>
                    <li><a class="dropdown-item" href="#">SPENISH</a></li>
                    <li><a class="dropdown-item" href="#">URDU</a></li>
                </ul>
            </div>
            <div class="btn-group ms-2">
                <button class="btn dropdown-toggle arrow-none bg-transparent shadow-none" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">$ USD <i data-feather="chevron-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">RUPEES</a></li>
                    <li><a class="dropdown-item" href="#">EURO</a></li>
                    <li><a class="dropdown-item" href="#">POUND</a></li>
                    <li><a class="dropdown-item" href="#">DIRHAM</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>