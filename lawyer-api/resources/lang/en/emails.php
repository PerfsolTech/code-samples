<?php

return [
    'layout' => [
        'need_help' => 'Need help? Contact',

    ],
    'auth' => [
        'activation' => [
            'subject' => 'Sign Up verification',
            'title' => 'Ammurapi Verify Account',
            'verify_your_email' => 'Verify Your Email',
            'i_verify' => 'I verify',
            'verify_text' => 'Before we get started, please verify that this is your email address:',
            'ignore_if_not_create' => 'If you didn\'t create this account, please ignore this email.',
        ],
        'social_activation' => [
            'subject' => 'Facebook verification',
            'title' => 'Ammurapi Verify Facebook account',
            'verify_your_email' => 'Verify Your Facebook account',
            'i_verify' => 'I verify',
            'verify_text' => 'Confirm connecting your FB account to this email',
            'ignore_if_not_create' => 'If you didn\'t create this account, please ignore this email.',
        ],
        'user_activated' => [
            'subject' => 'Account activated',
            'title' => 'Email successfully Verified',
            'body' => 'Your account has been activated successfully. You can now login and start contacting lawyers anywhere around the world.'
        ],
        'lawyer_activated' => [
            'subject' => 'Account activated',
            'title' => 'Email successfully Verified',
            'body' => 'Your account has been activated successfully. You can now login and start working with clients anywhere around the world.'
        ],
        'reset_password' => [
            'subject' => 'Forgot your password?',
            'title' => 'Reset password',
            'body' => 'In order to reset your password, just click on the button below.',
            'button' => 'Reset password',
        ],
        'reset_password_successful' => [
            'subject' => 'Password change',
            'title' => 'Password change',
            'body' => ' Your password was reset successfully.',
            'button' => 'Reset password',
        ],
        'lawyer_review' => [
            'subject' => 'You account submitted',
            'title' => 'You account submitted',
            'body' => 'Your account will be activated after admin review'
        ],

    ]
];