<?php /** @var  AView $this */ ?>
//set global sign of allowed 3d party cookies as true by default. This value might be overridden by test cookie js
var abc_cookie_allowed = true;
var abc_token_name = '<?php echo EMBED_TOKEN_NAME; ?>';
var abc_token_value = '';
if (window.abc_count === undefined) {
    window.abc_count = 0;
}
var embed_click_action = '<?php echo $embed_click_action;?>';
embed_click_action = (window.location !== window.parent.location) ? 'new_window' : embed_click_action;
var init = function () {
    // Localize jQuery
    var jQuery;
    if (window.abc_count > 0) {
        return false;
    } else {
        window.abc_count++;
    }

    /******** Load jQuery if not yet loaded (note: supported jquery >= 10 ) *********/
    if (window.jQuery === undefined) {
        script_loader("//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js");
        // Poll for jQuery to come into existence
        var scounter = 0;
        var checkReady = function (callback, second) {
            scounter++;
            if (window.jQuery !== undefined) {
                callback(jQuery);
            } else if (scounter <= 5) {
                window.setTimeout(function () {
                    checkReady(callback, second);
                }, 100);
            } else {
                //attempts limit reached
                scounter = 0;
                if (second !== undefined) {
                    second();
                }
            }
        };
        checkReady(
            function ($) {
                jQuery = window.jQuery.noConflict(true);
                main();
            },
            function ($) {
                //one more attempt to load local library
                script_loader("//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js");
                checkReady(function ($) {
                    jQuery = window.jQuery.noConflict(true);
                    main();
                });
            }
        );
    } else {
        // The jQuery version on the window is the one we want to use
        jQuery = window.jQuery;
        main();
    }

    /******** Called after jQuery has loaded ******/
    function scriptLoadHandler() {
        // Restore $ and window.jQuery to their previous values and store the
        // new jQuery in our local jQuery variable
        jQuery = window.jQuery.noConflict(true);
        main();
    }

    /******** Script loader function ********/
    function script_loader(url) {
        var script_tag = document.createElement('script');
        script_tag.setAttribute("type", "text/javascript");
        script_tag.setAttribute("src", url);
        // Try to find the head, otherwise default to the documentElement
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
        return script_tag;
    }

    /******** CSS loader function ********/
    function css_loader(url) {
        //check if css is already loaded
        var ss = document.styleSheets;
        for (var i = 0, max = ss.length; i < max; i++) {
            if (ss[i].href === url) return;
        }
        var css_tag = document.createElement('link');
        css_tag.setAttribute("rel",'stylesheet')
        css_tag.setAttribute("type",'text/css');
        css_tag.setAttribute("media","all");
        css_tag.setAttribute("href",url);
        // Try to find the head, otherwise default to the documentElement
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(css_tag);
    }

    /*****************************************/
    function abc_get_cookie() {
        var name = 'abantecart_token';
        var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    abc_token_value = abc_get_cookie();

    if (abc_token_value != undefined && abc_token_value.length > 0) {
        abc_cookie_allowed = false;
    }

    /********* AbanteCart url wrapper  ***********/
    function abc_process_url(url) {
        if (abc_cookie_allowed == false) {
            url += '&' + abc_token_name + '=' + abc_token_value;
        }
        return url;
    }

    function abc_process_request(url) {
        if (url.length < 1) {
            console.log('Abantecart embedded js-code: empty url requested!');
            return null;
        }
        url = abc_process_url(url);
        script_loader(url);
    }

    /******** function to append css-file with styles for embedded block from AbanteCart host ********/
    function abc_append_css(url) {
        if (url.length < 1) {
            console.log('AbanteCart embedded js-code: empty url for css requested!');
            return null;
        }
        css_loader(url);
    }

    /******** Main function ********/
    function main() {
        //set new custom jQuery in global space for included scripts (custom bootstrap)
        window.jQuery_abc = jQuery;

        jQuery(document).ready(function ($) {
            var modal = '';
            //for embedding with modal
            if (embed_click_action === 'modal') {
                /******** Load custom modal *********/
                css_loader("<?php echo AUTO_SERVER . $this->templateResource('/css/bootstrap.embed.css'); ?>");
                script_loader("<?php echo AUTO_SERVER . $this->templateResource('/js/bootstrap.embed.js'); ?>");

                // Load bootstrap custom modal (single instance)
                modal = '<div id="abc_embed_modal" class="abcmodal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">' +
                    '<div class="abcmodal-dialog abcmodal-lg">' +
                    '<div class="abcmodal-content">' +
                    '<div class="abcmodal-header">' +
                    '<div class="abcmodal-header-store">';
                <?php
                if($icon) { ?>
                modal += '<img src="<?php echo AUTO_SERVER; ?>resources/<?php echo $icon; ?>"/>&nbsp;';
                <?php } ?>
                modal += '&nbsp;' + <?php js_echo($store_name); ?>
                    + '</div><div class="abcmodal-header-menu">'
                    + '<a class="abcmodal-reload" href="#" data-href="<?php echo $account;?>">'
                    + <?php js_echo($text_account);?>
                    + '</a>&nbsp;&nbsp;'
                    + '|&nbsp;<a class="abcmodal-reload" href="#" data-href="<?php echo $cart;?>">'
                    + <?php js_echo($text_cart);?>
                    + '</a>&nbsp;&nbsp;'
                    + '|&nbsp;<a class="abcmodal-reload" href="#" data-href="<?php echo $checkout;?>">'
                    + <?php js_echo($text_checkout);?>
                    + '</a>&nbsp;&nbsp;'
                    + '</div>'
                    + '<button aria-hidden="true" data-dismiss="abcmodal" class="abcmodal_close" type="button">&times;</button>'
                    + '<h4 class="abcmodal-title"></h4>'
                    + '</div>'
                    + '<div class="abcmodal-body"><iframe id="amp_product_frame" width="100%" height="650px" frameBorder="0" allowpaymentrequest="true"></iframe>'
                    + '<div id="iframe_loading" display="none"></div>'
                    + '</div></div></div></div>'
                    + '<div class="abantecart-widget-cart"></div>';

                $('body').append(modal);
                <?php
                // do cookie-test if session id not retrieved from http-request
                if($test_cookie ?? false) { ?>
                abc_token_name = '<?php echo EMBED_TOKEN_NAME; ?>';
                abc_token_value = abc_get_cookie();
                var testcookieurl = '<?php echo $abc_embed_test_cookie_url; ?>';
                if (abc_token_value !== undefined && abc_token_value !== '') {
                    testcookieurl += '&<?php echo EMBED_TOKEN_NAME; ?>=' + abc_token_value;
                }
                abc_process_request(testcookieurl);
                <?php } ?>
                //bind events to modal
                $('#abc_embed_modal')
                    .on('click', '.abcmodal-reload', function (e) {
                        var url = $(this).attr('data-href');
                        url += abc_add_common_params($(this).closest('.abantecart-widget-container'));
                        loadIframe(url);
                        return false;
                    })
                    .on('shown.bs.abcmodal', function (e) {
                        var url = $(e.relatedTarget).attr('data-href');
                        url += abc_add_common_params($(e.relatedTarget).closest('.abantecart-widget-container'));
                        loadIframe(url);
                        $('#abc_embed_modal').abcmodal('show');
                        $('.abantecart-widget-cart').hide();
                    })
                    .on('hide.bs.abcmodal', function (e) {
                        //clear iframe
                        $('#abc_embed_modal').find('iframe').attr('src', '');
                        //reload cart
                        var $first_obj = $('.abantecart-widget-container').first();
                        var w_url = $first_obj.attr('data-url');
                        abc_populate_cart(w_url, abc_add_common_params($first_obj));
                    });
            } else {
                function popupwindow(url, title, w, h) {
                    var left = (screen.width/2)-(w/2);
                    var top = (screen.height/2)-(h/2);
                    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
                }
                //for direct-link mode
                $(document).on('click', "[data-toggle='abcmodal']", function () {
                    var url = $(this).attr('data-href');
                    if (url.length > 0) {
                        url += abc_add_common_params($(this).closest('.abantecart-widget-container'));
                        if (embed_click_action === 'same_window') {
                            window.location = url;
                        } else {
                            popupwindow(url,'embed-store', 960, 600);
                        }
                        return false;
                    }
                });
            }

            // Poll for abc_process_wrapper to come into existence
            var processReady = function (callback) {
                if (abc_process_wrapper !== undefined) {
                    callback();
                } else {
                    window.setTimeout(function () {
                        processReady(callback);
                    }, 100);
                }
            };

            processReady(function ($) {
                //fill data into embedded blocks
                abc_process_wrapper();
            });


            var loadIframe = function (url) {
                var $iframe = $("#abc_embed_modal iframe");
                //clear iframe
                $iframe.attr('src', '');
                $iframe.hide();

                $('#iframe_loading').show();
                $iframe.on('load',function () {
                    $('#iframe_loading').hide();
                    $iframe.show();
                });
                var d = new Date();
                //get href of modal caller

                var frame_url = abc_process_url(url + '&time_stamp=' + d.getTime());
                $iframe.attr("src", frame_url);
                return false;
            };

        });

        var abc_process_wrapper = function () {
            //using local jQuery
            $ = jQuery;
            var cntner = $('.abantecart-widget-container');
            cntner
                .show()
                .each(function () {
                    var c = $(this);
                    //widget url - base url of widget data (for case when 2 widgets from different domains on the same page)
                    var w_url = c.attr('data-url');
                    if (c.attr('data-css-url')) {
                        //load remote css for this embed block
                        abc_append_css(c.attr('data-css-url'));
                    }
                    abc_process_container(c, w_url);
                });

            //populate cart only 1 time
            var $first_obj = cntner.first();
            var main_url = $first_obj.attr('data-url');
            var url_params = abc_add_common_params($first_obj);
            abc_populate_cart(main_url, url_params);

            cntner.on("click", ".abantecart_addtocart", function (e) {
                var add_url = '';
                if ($(e.target).attr('data-toggle') === "abcmodal") {
                    add_url = $(e.target).attr('data-href');
                } else {
                    add_url = $(this).find('button').attr('data-href');
                }

                if ($('.abantecart_quantity input').val()) {
                    add_url += '&quantity=' + $('.abantecart_quantity input').val();
                }

                if ($(e.target).attr('data-toggle') === "abcmodal") {
                    $(e.target).attr('data-href', add_url);
                    return null;
                }

                abc_process_request(add_url);
                setTimeout(function () {
                    abc_populate_cart(main_url, url_params);
                }, 300)
                return false;
            });
        }

        //process data containers
        var abc_process_container = function (obj, w_url) {
            //using local jQuery
            $ = jQuery;
            var child = $(obj).children().first();
            if (child.is('[data-product-id]')) {
                abc_populate_product_item(child, w_url);
            } else if (child.is('[data-category-id]')) {
                abc_populate_categories_items($(obj).children(), w_url);
            } else if (child.is('[data-collection-id]')) {
                abc_populate_collection_items($(obj).children(), w_url);
            } else if (child.is('[data-manufacturer-id]')) {
                abc_populate_manufacturers_items($(obj).children(), w_url);
            }
        }

        var abc_populate_product_item = function (child, w_url) {
            //using local jQuery
            $ = jQuery;
            var product_id = child.attr('data-product-id');
            var d = new Date();
            //we need to know where we must to apply result
            var target_id = child.attr('id');
            child.attr('id', target_id);
            var url = w_url + '?rt=r/embed/js/product&product_id=' + product_id + '&target=' + target_id;
            if (child.is('[data-language]')) {
                url += '&language=' + child.attr('data-language');
            }
            if (child.is('[data-currency]')) {
                url += '&currency=' + child.attr('data-currency');
            }
            url += abc_add_common_params(child.parent('.abantecart-widget-container')) + '&time_stamp=' + d.getTime();
            abc_process_request(url);
        }

        var abc_populate_categories_items = function (children, w_url) {
            //using local jQuery
            $ = jQuery;
            var d = new Date();
            var url = w_url + '?rt=r/embed/js/categories';
            var target_id, category_id;

            $(children).each(function () {
                if ($(this).is('[data-category-id]')) {
                    var cid = $(this).attr('data-category-id');
                    url += '&category_id[]=' + cid + '&target_id[' + cid + ']=' + $(this).attr('id');
                    if ($(this).is('[data-language]')) {
                        url += '&language=' + $(this).attr('data-language');
                    }
                    if ($(this).is('[data-currency]')) {
                        url += '&currency=' + $(this).attr('data-currency');
                    }
                }
            })
            url += abc_add_common_params(children.first().parent('.abantecart-widget-container')) + '&time_stamp=' + d.getTime();
            abc_process_request(url);
        }

        var abc_populate_collection_items = function (children, w_url) {
            //using local jQuery
            $ = jQuery;
            var d = new Date();
            var url = w_url + '?rt=r/embed/js/collection';
            var target_id, collection_id;
            if ($(children).is('[data-collection-id]')) {
                var cid = $(children).attr('data-collection-id');
                url += '&collection_id=' + cid + '&target_id=' + $(children).attr('id');
                if ($(children).is('[data-language]')) {
                    url += '&language=' + $(children).attr('data-language');
                }
                if ($(children).is('[data-currency]')) {
                    url += '&currency=' + $(children).attr('data-currency');
                }
                if ($(children).has('.abantecart_name').length) {
                    url += '&product_name=1';
                }
                if ($(children).has('.abantecart_image').length) {
                    url += '&product_image=1';
                }
                if ($(children).has('.abantecart_price').length) {
                    url += '&product_price=1';
                }
                var limit = $(children).find('input[name=limit]');
                if (limit.length) {
                    url += '&limit='+ limit.val();
                }
            }

            url += abc_add_common_params(children.first().parent('.abantecart-widget-container')) + '&time_stamp=' + d.getTime();
            abc_process_request(url);
        }

        var abc_populate_manufacturers_items = function (children, w_url) {
            //using local jQuery
            $ = jQuery;
            var d = new Date();
            var url = w_url + '?rt=r/embed/js/manufacturers';
            var target_id, manufacturer_id;

            $(children).each(function () {
                if ($(this).is('[data-manufacturer-id]')) {
                    var cid = $(this).attr('data-manufacturer-id');
                    url += '&manufacturer_id[]=' + cid + '&target_id[' + cid + ']=' + $(this).attr('id');
                    if ($(this).is('[data-language]')) {
                        url += '&language=' + $(this).attr('data-language');
                    }
                    if ($(this).is('[data-currency]')) {
                        url += '&currency=' + $(this).attr('data-currency');
                    }
                }
            })
            url += abc_add_common_params(children.first().parent('.abantecart-widget-container')) + '&time_stamp=' + d.getTime();
            abc_process_request(url);
        }

        var abc_populate_cart = function (w_url, url_params) {
            if (!w_url) return;
            //using local jQuery
            $ = jQuery;

            var url = w_url + '?rt=r/embed/js/cart' + url_params;
            abc_process_request(url);
        }

        var abc_add_common_params = function (obj) {
            var append = '';
            var language = obj.attr('data-language');
            var currency = obj.attr('data-currency');
            if (language && language.length > 0) {
                append += '&language=' + language;
            }
            if (currency && currency.length > 0) {
                append += '&currency=' + currency;
            }
            return append;
        }
    }
};

if (checkLoaded()) {
    //if main window is already loaded fire up
    init();
} else {
    window.addEventListener("load", function () {
        // wait for main window to finish loading
        init();
    });
}

function checkLoaded() {
    return document.readyState === "complete" || document.readyState === "interactive";
}
