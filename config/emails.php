<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Templates Configuration
    |--------------------------------------------------------------------------
    */

    'templates' => [
        'unsubscribe_footer' => '
            <hr style="margin: 30px 0; border: none; border-top: 2px solid #333333;">
            <p style="font-size: 12px; color: #666;">
               Don\'t need these newsletters in your mailbox? Then let\'s get you out of here - <a href="{{ $unsubscribeUrl }}" target="_blank" style="color: #666;">Unsubscribe</a><br />
               If you feel these newsletters were sent to you in error, please contact us at <a href="mailto:info@realcoolphototours.com" style="color: #666;">RealCoolPhotoTours</a>

            </p>
        ',

        'wrapper_styles' => [
            'font_family' => 'Arial, sans-serif',
            'line_height' => '1.6',
            'font-size' => '15px',
            'color' => '#333',
            'max_width' => '600px',
            'padding' => '20px'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Configuration
    |--------------------------------------------------------------------------
    */

    'tokens' => [
        'length' => 32,
        'expiry_days' => null, // null = never expires
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'template_ttl' => 3600, // 1 hour
        'enabled' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    'security' => [
        'sanitize_input' => true,
        'validate_tokens' => true,
        'rate_limit' => [
            'token_generation' => 10, // per minute
            'template_saves' => 20 // per minute
        ]
    ]
];
