<?php

/**
 * Available possible values for application
 */

return [
    'users' => [
        'status' => [
            'active',
            'blocked',
            'deleted',
        ]
    ],
    'albums' => [
        'status' => [
            'published',
            'default',
            'deleted',
        ]
    ],
    'activities' => [
        'type' => [
            'commented',
            'followed',
            'liked',
            'posted',
            'new_message',
            'tagged_on_post'
        ]
    ],
    'adds' => [
        'status' => [
            'created',
            'active',
            'expired',
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
    'itineraries' => [
        'status' => [
            'published',
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
    ],
    'notification_settings' => [
        'sms_notification' => 1,
        'email_notification' => 1,
        'push_notification' => 1,
        'post_like_notification' => 1,
        'post_comment_notification' => 1,
        'follower_notification' => 1,
        'new_message_notification' => 1,
    ]

];

