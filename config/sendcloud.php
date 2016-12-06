<?php
/**
 * This file is part of HyanCat/SendCloud.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */

return [
    'api'  => [
        'user' => env('SENDCLOUD_API_USER'),
        'key'  => env('SENDCLOUD_API_KEY'),
    ],
    'from' => [
        'address' => env('SENDCLOUD_FROM_ADDR'),
        'name'    => env('SENDCLOUD_FROM_NAME'),
    ]
];
