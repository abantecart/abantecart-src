<div class="content-footer-menu navbar ">
<?php
    $contents[] = [
            'text' => $text_contact,
            'href' => $contact
    ];
    $contents[] = [
            'text' => $text_sitemap,
            'href' => $sitemap
    ];
    $contents[] = [
            'text' => $logged ? $text_logout : $text_login,
            'href' => $logged ? $logout : $login
    ];
    $contents[] = [
            'text' => $text_account,
            'href' => $account
    ];
    $contents[] = [
            'text' => $text_cart,
            'href' => $cart
    ];
    $opt['submenu_class'] = 'dropup';
    echo renderSFMenuNv($contents,0,'',$opt);
?>
</div>