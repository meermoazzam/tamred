<?php

/**
 * Available possible values for application
 */

return [
    'users' => [
        'status' => [
            'active',
            'blocked',
        ]
    ],
    'albums' => [
        'status' => [
            'published',
            'default',
            'deleted',
        ]
    ],
    'posts' => [
        'status' => [
            'draft',
            'published',
            'archived',
            'deleted',
        ]
    ],
    'comments' => [
        'status' => [
            'published',
            'archived',
            'deleted',
        ]
    ],
    'user_meta' => [
        'terms_and_conditions' => true,
        'privacy_policy' => true,
        'marketing' => true,
        'reset_password_otp' => null,
        'verify_email_otp' => null,
    ]

];

