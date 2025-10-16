<div class="content-footer footer-content-menu">
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

    echo renderNVNestedMenu($contents,['parent_css' => ' parent-ul border-0 d-flex mx-auto list-unstyled align-items-start mb-0 justify-content-between px-0' ]);
?>
</div>
