/* 
 Credit Card Validation JavaScript Class
 aCCValidator class to validate details on creditcard form
 Features: Check credit card type while entering numbers. Full number validation, Validation of name, and CVV data.
 Easy and clean UI based on bootstrap v3

 Developer: Pavel Rojkov (projkov@abantecart.com)


 Credit Card Validation class
 */

(function ($) {
    $.aCCValidator = {
        defaults: {
            /*
             Array with supported credit cards. New types can be added
             Refer to: http://en.wikipedia.org/wiki/Bank_card_number

             Name:	    Credit card name
             Code: 		Text code that is used in form select form element
             Length:     List of possible valid lengths of the card number for the card
             prefixes:   List of possible prefixes for the card
             checkdigit: Boolean to say whether there is a check digit
             */
            cards: [{
                name: "Visa",
                code: "visa",
                length: "13,16",
                prefixes: "4",
                checkdigit: true
            }, {
                name: "DinersClub",
                code: "diners",
                length: "14,16",
                prefixes: "36,38,39,54,55",
                checkdigit: true
            }, {
                name: "DinersClub",
                code: "dc",
                length: "14,16",
                prefixes: "36,38,39,54,55",
                checkdigit: true
            }, {
                name: "Diners Club Carte Blanche",
                code: "cblanche",
                length: "14",
                prefixes: "300,301,302,303,304,305",
                checkdigit: true
            }, {
                name: "MasterCard",
                code: "mc",
                length: "16",
                prefixes: "51,52,53,54,55,2221,2222,2223,2224,2225,2226,2227,2228,2229,2230,2231,2232,2233,2234,2235,2236,2237,2238,2239,2240,2241,2242,2243,2244,2245,2246,2247,2248,2249,2250,2251,2252,2253,2254,2255,2256,2257,2258,2259,2260,2261,2262,2263,2264,2265,2266,2267,2268,2269,2270,2271,2272,2273,2274,2275,2276,2277,2278,2279,2280,2281,2282,2283,2284,2285,2286,2287,2288,2289,2290,2291,2292,2293,2294,2295,2296,2297,2298,2299,2300,2301,2302,2303,2304,2305,2306,2307,2308,2309,2310,2311,2312,2313,2314,2315,2316,2317,2318,2319,2320,2321,2322,2323,2324,2325,2326,2327,2328,2329,2330,2331,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2347,2348,2349,2350,2351,2352,2353,2354,2355,2356,2357,2358,2359,2360,2361,2362,2363,2364,2365,2366,2367,2368,2369,2370,2371,2372,2373,2374,2375,2376,2377,2378,2379,2380,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2397,2398,2399,2400,2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,2413,2414,2415,2416,2417,2418,2419,2420,2421,2422,2423,2424,2425,2426,2427,2428,2429,2430,2431,2432,2433,2434,2435,2436,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453,2454,2455,2456,2457,2458,2459,2460,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2478,2479,2480,2481,2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496,2497,2498,2499,2500,2501,2502,2503,2504,2505,2506,2507,2508,2509,2510,2511,2512,2513,2514,2515,2516,2517,2518,2519,2520,2521,2522,2523,2524,2525,2526,2527,2528,2529,2530,2531,2532,2533,2534,2535,2536,2537,2538,2539,2540,2541,2542,2543,2544,2545,2546,2547,2548,2549,2550,2551,2552,2553,2554,2555,2556,2557,2558,2559,2560,2561,2562,2563,2564,2565,2566,2567,2568,2569,2570,2571,2572,2573,2574,2575,2576,2577,2578,2579,2580,2581,2582,2583,2584,2585,2586,2587,2588,2589,2590,2591,2592,2593,2594,2595,2596,2597,2598,2599,2600,2601,2602,2603,2604,2605,2606,2607,2608,2609,2610,2611,2612,2613,2614,2615,2616,2617,2618,2619,2620,2621,2622,2623,2624,2625,2626,2627,2628,2629,2630,2631,2632,2633,2634,2635,2636,2637,2638,2639,2640,2641,2642,2643,2644,2645,2646,2647,2648,2649,2650,2651,2652,2653,2654,2655,2656,2657,2658,2659,2660,2661,2662,2663,2664,2665,2666,2667,2668,2669,2670,2671,2672,2673,2674,2675,2676,2677,2678,2679,2680,2681,2682,2683,2684,2685,2686,2687,2688,2689,2690,2691,2692,2693,2694,2695,2696,2697,2698,2699,2700,2701,2702,2703,2704,2705,2706,2707,2708,2709,2710,2711,2712,2713,2714,2715,2716,2717,2718,2719,2720",
                checkdigit: true
            }, {
                name: "MasterCard",
                code: "mastercard",
                length: "16",
                prefixes: "51,52,53,54,55,2221,2222,2223,2224,2225,2226,2227,2228,2229,2230,2231,2232,2233,2234,2235,2236,2237,2238,2239,2240,2241,2242,2243,2244,2245,2246,2247,2248,2249,2250,2251,2252,2253,2254,2255,2256,2257,2258,2259,2260,2261,2262,2263,2264,2265,2266,2267,2268,2269,2270,2271,2272,2273,2274,2275,2276,2277,2278,2279,2280,2281,2282,2283,2284,2285,2286,2287,2288,2289,2290,2291,2292,2293,2294,2295,2296,2297,2298,2299,2300,2301,2302,2303,2304,2305,2306,2307,2308,2309,2310,2311,2312,2313,2314,2315,2316,2317,2318,2319,2320,2321,2322,2323,2324,2325,2326,2327,2328,2329,2330,2331,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2347,2348,2349,2350,2351,2352,2353,2354,2355,2356,2357,2358,2359,2360,2361,2362,2363,2364,2365,2366,2367,2368,2369,2370,2371,2372,2373,2374,2375,2376,2377,2378,2379,2380,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2397,2398,2399,2400,2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,2413,2414,2415,2416,2417,2418,2419,2420,2421,2422,2423,2424,2425,2426,2427,2428,2429,2430,2431,2432,2433,2434,2435,2436,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453,2454,2455,2456,2457,2458,2459,2460,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2478,2479,2480,2481,2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496,2497,2498,2499,2500,2501,2502,2503,2504,2505,2506,2507,2508,2509,2510,2511,2512,2513,2514,2515,2516,2517,2518,2519,2520,2521,2522,2523,2524,2525,2526,2527,2528,2529,2530,2531,2532,2533,2534,2535,2536,2537,2538,2539,2540,2541,2542,2543,2544,2545,2546,2547,2548,2549,2550,2551,2552,2553,2554,2555,2556,2557,2558,2559,2560,2561,2562,2563,2564,2565,2566,2567,2568,2569,2570,2571,2572,2573,2574,2575,2576,2577,2578,2579,2580,2581,2582,2583,2584,2585,2586,2587,2588,2589,2590,2591,2592,2593,2594,2595,2596,2597,2598,2599,2600,2601,2602,2603,2604,2605,2606,2607,2608,2609,2610,2611,2612,2613,2614,2615,2616,2617,2618,2619,2620,2621,2622,2623,2624,2625,2626,2627,2628,2629,2630,2631,2632,2633,2634,2635,2636,2637,2638,2639,2640,2641,2642,2643,2644,2645,2646,2647,2648,2649,2650,2651,2652,2653,2654,2655,2656,2657,2658,2659,2660,2661,2662,2663,2664,2665,2666,2667,2668,2669,2670,2671,2672,2673,2674,2675,2676,2677,2678,2679,2680,2681,2682,2683,2684,2685,2686,2687,2688,2689,2690,2691,2692,2693,2694,2695,2696,2697,2698,2699,2700,2701,2702,2703,2704,2705,2706,2707,2708,2709,2710,2711,2712,2713,2714,2715,2716,2717,2718,2719,2720",
                checkdigit: true
            }, {
                name: "American Express",
                code: "amex",
                length: "15",
                prefixes: "34,37",
                checkdigit: true
            }, {
                name: "China UnionPay",
                code: "unionpay",
                length: "16,17,18,19",
                prefixes: "62",
                checkdigit: false
            }, {
                name: "Discover",
                code: "discover",
                length: "16",
                prefixes: "6011,622,64,65",
                checkdigit: true
            }, {
                name: "JCB",
                code: "jcb",
                length: "16",
                prefixes: "35",
                checkdigit: true
            }, {
                name: "InterPayment",
                code: "interpayment",
                length: "16,17,18,19",
                prefixes: "636",
                checkdigit: true
            }, {
                name: "InstaPayment",
                code: "instapayment",
                length: "16",
                prefixes: "637,638,639",
                checkdigit: true
            }, {
                name: "Switch",
                code: "switch",
                length: "16,18,19",
                prefixes: "4903,4905,4911,4936,564182,633110,6333,6759",
                checkdigit: true
            }, {
                name: "Maestro",
                code: "maestro",
                length: "12,13,14,15,16,18,19",
                prefixes: "5018,5020,5038,6304,6759,6761,6762,6763",
                checkdigit: true
            }, {
                name: "VisaElectron",
                code: "visaelectron",
                length: "16",
                prefixes: "4026,417500,4508,4844,4913,4917",
                checkdigit: true
            }, {
                name: "Dankort",
                code: "dankort",
                length: "16",
                prefixes: "5019",
                checkdigit: true
            }, {
                name: "LaserCard",
                code: "laser",
                length: "16,17,18,19",
                prefixes: "6304,6706,6771,6709",
                checkdigit: true
            }],
            cc_field_name: 'cc_owner',
            name_min: 2,
            cvv_min: 3,
            cvv_max: 4,
            cc_field_number: 'cc_number',
            cc_field_type: 'cc_type',
            cc_field_cvv: 'cc_cvv2',
            cc_field_month: 'cc_expire_date_month',
            cc_field_year: 'cc_expire_date_year',
            wrapper: '<div class="right-inner-addon"></div>',
            warning: '<i class="fa fa-exclamation"></i>',
            success: '<i class="fa fa-check"></i>',
            error_class: 'has-error',
            success_class: 'has-success',
        },
    };

    $.fn.aCCValidator = function (op) {
        var o = $.extend({}, $.aCCValidator.defaults, op);

        /* Full validation */
        $.aCCValidator.validate = function ($form) {
            var failed = false;
            $form.find("input, textarea, select").each(function () {
                var $field = $(this);
                if ($field.attr('name') == o.cc_field_name) {
                    if (!$.aCCValidator.checkCCName($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_number) {
                    if (!$.aCCValidator.checkCCNumber($field)) {
                        failed = true;
                    }
                }

                if ($field.attr('name') == o.cc_field_cvv) {
                    if (!$.aCCValidator.checkCVV($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_month) {
                    if (!$.aCCValidator.checkExp($field)) {
                        failed = true;
                    }
                }
                if ($field.attr('name') == o.cc_field_year) {
                    if (!$.aCCValidator.checkExp($field)) {
                        failed = true;
                    }
                }
            });
            if (failed) {
                return false;
            } else {
                return true;
            }
        };

        /* Prefill creditcard type based on first set of numbers */
        $.aCCValidator.precheckCCNumber = function ($el) {
            var number = $el.val();
            //remove spaces
            number = number.replace(/\s/g, "");
            var $ig = $el.closest('.input-group');
            var rgx_number = /^\d+$/;
            if (!rgx_number.test(number)) {
                show_error($el, '.input-group');
            } else if (number.length > 3) {
                //check for cc type based on the number
                $ig.removeClass(o.error_class);
                hide_addon($el);
                var cc_type = lookupCreditCardType(number);
                if (cc_type) {
                    select_cctype($el, cc_type);
                } else {
                    hide_addon($el);
                }
            }

        };

        /* Validate full number (when leave the field) */
        $.aCCValidator.checkCCNumber = function ($el) {
            //Check if we have a number
            var rgx_number = /^[0-9]{13,19}$/;
            var number = $el.val();
            //remove spaces
            number = number.replace(/\s/g, "");
            if (!rgx_number.test(number)) {
                show_error($el, '.input-group');
                return false;
            }
            if (!CheckDigits(number)) {
                show_error($el, '.input-group');
                return false;
            }
            //all good
            show_success($el, '.input-group');
            return true;
        };

        /* Validate Name */
        $.aCCValidator.checkCCName = function ($el, mode) {
            var $fg = $el.closest('.form-group');
            if (mode == 'reset') {
                $fg.removeClass(o.error_class);
                $fg.removeClass(o.success_class);
                hide_addon($el);
                return false;
            }
            if ($el.val().length < o.name_min) {
                show_error($el, '.form-group');
                return false;
            } else {
                show_success($el, '.form-group');
                return true;
            }
        };

        /* Validate CVV */
        $.aCCValidator.checkCVV = function ($el, mode) {
            var $fg = $el.closest('.form-group');
            if (mode == 'reset') {
                $fg.removeClass(o.error_class);
                $fg.removeClass(o.success_class);
                hide_addon($el);
                return false;
            }
            if ($el.val().length < o.cvv_min || $el.val().length > o.cvv_max) {
                show_error($el, '.form-group');
                return false;
            } else {
                show_success($el, '.form-group');
                return true;
            }
        };

        /* Validate exp month */
        $.aCCValidator.checkExp = function ($el, mode) {
            var $ig = $el.closest('.input-group');
            if (mode == 'reset') {
                $ig.removeClass(o.error_class);
                $ig.removeClass(o.success_class);
                return false;
            }
            if (!$el.val()) {
                show_error($el, '.input-group', 'no_icon');
                return false;
            } else {
                show_success($el, '.input-group', 'no_icon');
                return true;
            }
        };
        $.aCCValidator.checkType = function ($el, mode) {
            var $ig = $el.closest('.input-group');
            if (mode == 'reset') {
                $ig.removeClass(o.error_class);
                $ig.removeClass(o.success_class);
                return false;
            }
            if (!$el.val() || $el.val()=='notfound') {
                show_error($el, '.input-group', 'no_icon');
                return false;
            } else {
                show_success($el, '.input-group', 'no_icon');
                return true;
            }
        };

        select_cctype = function ($el, cc_type) {
            var $cct = $el.closest('form').find('[name=' + o.cc_field_type + ']');
            if ($cct.length) {
                //select cc_type in the select box
                var empty;
                var found;
                if($cct.attr('type') == 'hidden'){
                    $cct.val(cc_type.toLowerCase());
                }else {
                    $cct.find('option').each(function () {
                        if ($(this).val().toLowerCase() == cc_type.toLowerCase()) {
                            $(this).prop('selected', true);
                            found = true;
                            return;
                        } else if ($(this).val() == 'notfound') {
                            empty = true;
                        }
                    });
                }
                if (found) {
                    show_success($cct, '.input-group', 'no_icon');
                } else {
                    //nothing found
                    if (!empty) {
                        $cct.append('<option value="notfound"></option>');
                        $cct.find('option').each(function () {
                            if ($(this).val() == 'notfound') {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                    show_error($cct, '.input-group', 'no_icon');
                }
            }
        };

        /* Show bootstrap field input-group-addon */
        show_addon = function ($el, html) {
            var $ig = $el.closest('.input-group');
            if (!$ig.find('.input-group-addon').length) {
                $ig.append(o.wrapper);
            }
            $ig.find('.input-group-addon').html(html);
        };

        hide_addon = function ($el) {
            var $ig = $el.closest('.input-group');
            if ($ig.find('.input-group-addon').length) {
                $ig.find('.input-group-addon').remove();
            }
        };

        show_success = function ($el, selector, mode) {
            var $att = $el.closest(selector);
            $att.removeClass(o.error_class);
            $att.addClass(o.success_class);
            if (mode != 'no_icon') {
                hide_addon($el);
                show_addon($el, o.success);
            }
        };

        show_error = function ($el, selector, mode) {
            var $att = $el.closest(selector);
            $att.removeClass(o.success_class);
            $att.addClass(o.error_class);
            if (mode != 'no_icon') {
                hide_addon($el);
                show_addon($el, o.warning);
            }
        };

        lookupCreditCardType = function (cardnumber) {
            //look for matching cc type backwards
            for (i = o.cards.length - 1; i >= 0; i--) {
                prefix_arr = o.cards[i].prefixes.split(",");
                //Check if number begins with prefix
                for (j = 0; j < prefix_arr.length; j++) {
                    var exp = new RegExp("^" + prefix_arr[j]);
                    if (exp.test(cardnumber)) {
                        //found matching
                        return o.cards[i].code.toLowerCase();
                    }
                }
            }
            return false;
        };

        CheckDigits = function (cardnumber) {
            var card_rec;
            //look for matching cc type backwards
            for (i = o.cards.length - 1; i >= 0; i--) {
                prefix_arr = o.cards[i].prefixes.split(",");
                //Check if number begins with prefix
                for (j = 0; j < prefix_arr.length; j++) {
                    var exp = new RegExp("^" + prefix_arr[j]);
                    if (exp.test(cardnumber)) {
                        //found matching
                        card_rec = o.cards[i];
                    }
                }
            }

            // Now check the modulus 10 check digit - if required
            if (card_rec && card_rec.checkdigit) {
                var checksum = 0;
                var j = 1;

                // Process each digit one by one starting on the right
                var calc;
                for (i = card_rec.length - 1; i >= 0; i--) {
                    // Get the next digit and multiply by 1 or 2 on alternative digits.
                    calc = Number(cardnumber.charAt(i)) * j;
                    // add 1 to the checksum total
                    if (calc > 9) {
                        checksum = checksum + 1;
                        calc = calc - 10;
                    }
                    // Add the units element to the checksum total
                    checksum = checksum + calc;
                    // Switch the value of j
                    if (j == 1) {
                        j = 2
                    } else {
                        j = 1
                    }
                    ;
                }

                // if checksum is divisible by 10, it is a valid modulus 10.
                if (checksum % 10 != 0) {
                    return false;
                }

                // See if the length is valid for this card
                var lengths = card_rec.length.split(",");
                for (j = 0; j < lengths.length; j++) {
                    if (cardnumber.length == lengths[j]) {
                        return true;
                    }
                }
                return false;

            } else {
                //unsupported card
                return false;
            }
        }

    };

})(jQuery);


/* Listen events for form with validate-creditcard css class */

jQuery(document).ready(function () {
    //event to log creditcard entering
    $('form.validate-creditcard').aCCValidator({});

    $('form.validate-creditcard [name=cc_number]').bind({
        change: function () {
            //check as number is entered
            $.aCCValidator.precheckCCNumber($(this));
        },
        blur: function () {
            //check full number as lost focus
            $.aCCValidator.checkCCNumber($(this));
        },
        keyup: function (e) {
            if (e.keyCode === 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else if ($(this).val()) {
                $.aCCValidator.precheckCCNumber($(this));
            }
        }
    });

    $('form.validate-creditcard [name=cc_owner]').bind({
        change: function () {
            $.aCCValidator.checkCCName($(this), 'reset');
        },
        blur: function () {
            $.aCCValidator.checkCCName($(this));
        },
        keyup: function (e) {
            if (e.keyCode === 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else {
                $.aCCValidator.checkCCName($(this), 'reset');
            }
        }
    });

    $('form.validate-creditcard [name=cc_cvv2]').bind({
        change: function () {
            $.aCCValidator.checkCVV($(this), 'reset');
        },
        blur: function () {
            $.aCCValidator.checkCVV($(this));
        },
        keyup: function (e) {
            if (e.keyCode === 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else {
                $.aCCValidator.checkCVV($(this), 'reset');
            }
        }
    });
    $('form.validate-creditcard #cc_type, form.validate-creditcard [name="cc_type"]').bind({
        change: function () {
            $.aCCValidator.checkType($(this), 'reset');
        },
        blur: function () {
            $.aCCValidator.checkType($(this));
        },
        keyup: function (e) {
            if (e.keyCode === 13) {
                //enter pressed. validate all data
                $('form.validate-creditcard').submit();
            } else {
                $.aCCValidator.checkType($(this), 'reset');
            }
        }
    });

});
