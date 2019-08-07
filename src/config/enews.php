<?php

return [
    'theme' => 'bootstrap3',
    'layout' => 'layouts.admin.master',
    'layout_section' => 'content',

    // listing of days that appears on form
    'days' => [
        0 => 'Default',
    ],

    'enewsletters' => [
        [
            'key' => 'default',

            'label' => 'Default',

            // publish API parameters
            'publish_parameters' => [
                'tags' => 15823,
                'pageSize' => 50,
            ],

            // map day indexes to zones
            'zones' => [
                0 => 614984,
            ],

            // adestra list available for sending
            'lists' => [
                16359 => 'Test List',
                65 => 'Greenhead',
            ],

            // adestra campaign options
            'campaign_options' => [
                'name_prefix' => 'Greenhead Enews ', // this is a NOT an Adestra campaign option
                'project_id' => 52,
                'domain' => 'email.greenhead.net',
                'from_prefix' => 'mail',
                'from_name' => 'Greenhead',
                'auto_tracking' => 1,
                'user_from' => 1,
                'from_address' => 'enews@greenhead.net',
                'user_reply' => 1,
                'reply_address' => 'enews@greenhead.net',
                'reply_name' => 'Greenhead',
                'unsub_list' => 7,
            ],
        ]
    ],
];
