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
    'media' => [
        'status' => [
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
    'chat_messages' => [
        'status' => [
            'published',
            'deleted',
        ]
    ],
    'chat_participants' => [
        'status' => [
            'active',
            'left',
        ],
        'message_status' => [
            '0', // sent to server
            '1', // message delivered by user
            '2', // message read by user
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

