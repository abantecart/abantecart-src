<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

const USPS_CLASSES = [
    'domestic' => [
        0 => 'First-Class', //deprecated
        1 => 'Priority Mail',
        2 => 'Priority Mail Express 1-Day Hold For Pickup',
        3 => 'Priority Mail Express 1-Day',
        4 => 'Parcel Post',
        5 => 'Bound Printed Matter',
        6 => 'Media Mail Parcel',
        7 => 'Library Mail Parcel',
        12 => 'First-Class Postcard Stamped',
        13 => 'Priority Mail Express 1-Day Flat Rate Envelope',
        15 => 'First-Class Mail Large Postcards',
        16 => 'Priority Mail Flat Rate Envelope',
        17 => 'Priority Mail Medium Flat Rate Box',
        18 => 'Priority Mail Keys and IDs',
        19 => 'First-Class Keys and IDs',
        22 => 'Priority Mail Large Flat Rate Box',
        23 => 'Express Mail Sunday/Holiday',
        25 => 'Express Mail Flat-Rate Envelope Sunday/Holiday',
        27 => 'Priority Mail Express 1-Day Flat Rate Envelope Hold For Pickup',
        28 => 'Priority Mail Small Flat Rate Box',
        29 => 'Priority Mail Padded Flat Rate Envelope',
        30 => 'Priority Mail Express 1-Day Legal Flat Rate Envelope',
        31 => 'Priority Mail Express 1-Day Legal Flat Rate Envelope Hold For Pickup',
        33 => 'Priority Mail Hold For Pickup',
        34 => 'Priority Mail Large Flat Rate Box Hold For Pickup',
        35 => 'Priority Mail Medium Flat Rate Box Hold For Pickup',
        36 => 'Priority Mail Small Flat Rate Box Hold For Pickup',
        37 => 'Priority Mail Flat Rate Envelope Hold For Pickup',
        38 => 'Priority Mail Gift Card Flat Rate Envelope',
        39 => 'Priority Mail Gift Card Flat Rate Envelope Hold For Pickup',
        40 => 'Priority Mail Window Flat Rate Envelope',
        41 => 'Priority Mail Window Flat Rate Envelope Hold For Pickup',
        42 => 'Priority Mail Small Flat Rate Envelope',
        43 => 'Priority Mail Small Flat Rate Envelope Hold For Pickup',
        44 => 'Priority Mail Legal Flat Rate Envelope',
        45 => 'Priority Mail Legal Flat Rate Envelope Hold For Pickup',
        46 => 'Priority Mail Padded Flat Rate Envelope Hold For Pickup',

        62 => 'Priority Mail Express 1-Day Padded Flat Rate Envelope',
        63 => 'Priority Mail Express 1-Day Padded Flat Rate Envelope Hold For Pickup',

        1058 => 'USPS Ground Advantage',
        1096 => 'USPS Ground Advantage Cubic',

        2058 => 'USPS Ground Advantage Hold For Pickup',
        2096 => 'USPS Ground Advantage Cubic Hold For Pickup',

        4001 => 'Priority Mail Express 1-Day HAZMAT',
        4010 => 'Priority Mail HAZMAT',
        4012 => 'Priority Mail Large Flat Rate Box HAZMAT',
        4013 => 'Priority Mail Medium Flat Rate Box HAZMAT',
        4014 => 'Priority Mail Small Flat Rate Box HAZMAT',
        4058 => 'USPS Ground Advantage HAZMAT',
        4096 => 'USPS Ground Advantage Cubic HAZMAT',

        6001 => 'Priority Mail Express 1-Day Parcel Locker',
        6010 => 'Priority Mail Parcel Locker',
        6012 => 'Priority Mail Large Flat Rate Box Parcel Locker',
        6013 => 'Priority Mail Medium Flat Rate Box Parcel Locker',
        6014 => 'Priority Mail Small Flat Rate Box Parcel Locker',

        6058 => 'USPS Ground Advantage Parcel Locker',
        6076 => 'Media Mail Parcel Parcel Locker',
        6075 => 'Library Mail Parcel Parcel Locker',
        6096 => 'USPS Ground Advantage Cubic Parcel Locker',
    ],
    'international' => [
        1 => 'Priority Mail Express International',
        2 => 'Priority Mail International',
        3 => 'Priority Mail International',
        4 => 'Global Express Guaranteed (GXG)',
        5 => 'Global Express Guaranteed Document used',
        6 => 'Global Express Guaranteed Non-Document Rectangular shape',
        7 => 'Global Express Guaranteed Non-Document Non-Rectangular',
        8 => 'Priority Mail International Flat Rate Envelope',
        9 => 'Priority Mail International Medium Flat Rate Box',
        10 => 'Priority Mail Express International Flat Rate Envelope',
        11 => 'Priority Mail International Large Flat Rate Box',
        12 => 'USPS GXG Envelopes',
        13 => 'First Class Mail International Letters',
        14 => 'First Class Mail International Flats',
        15 => 'First-Class Package International Service',
        16 => 'Priority Mail International Small Flat Rate Box',
        17 => 'Priority Mail Express International Legal Flat Rate Envelope',
        18 => 'Priority Mail International Gift Card Flat Rate Envelope',
        19 => 'Priority Mail International Window Flat Rate Envelope',
        20 => 'Priority Mail International Small Flat Rate Envelope',
        21 => 'Postcards',
        22 => 'Priority Mail International Legal Flat Rate Envelope',
        23 => 'Priority Mail International Padded Flat Rate Envelope',
        24 => 'Priority Mail International DVD Flat Rate priced box',
        25 => 'Priority Mail International Large Video Flat Rate priced box',
        27 => 'Priority Mail Express International Padded Flat Rate Envelope',
        28 => 'Airmail M-Bag'
    ]
];

$controllers = [
    'storefront' => [],
    'admin'      => [
        'pages/extension/default_usps',
        'responses/extension/default_usps_save',
    ]
];

$models = [
    'storefront' => ['extension/default_usps'],
    'admin'      => [],
];

$languages = [
    'storefront' => [
        'default_usps/default_usps',
    ],
    'admin'      => [
        'default_usps/default_usps',
    ],
];

$templates = [
    'storefront' => [],
    'admin'      => [
        'pages/extension/default_usps.tpl'
    ]
];