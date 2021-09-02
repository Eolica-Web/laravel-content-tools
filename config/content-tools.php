<?php

return [
    'routes' => [
        'prefix' => 'content-tools',
        'middleware' => ['web'],
    ],

    'editor' => [
        'default_tools' => [
            [
                'bold',
                'italic',
                'link',
                'align-left',
                'align-center',
                'align-right',
            ],
            [
                'heading',
                'subheading',
                'paragraph',
                'unordered-list',
                'ordered-list',
                'table',
                'indent',
                'unindent',
                'line-break',
            ],
            [
                'preformatted',
            ],
            [
                'undo',
                'redo',
                'remove',
            ],
        ],

        'default_video_width'   => 400,
        'default_video_height'  => 300,

        'highlight_hold_duration' => 2000,

        'min_crop' => 10,

        'restricted_attributes' => [
            '*'         => [],
            'img'       => ['height', 'width', 'src', 'data-ce-max-width', 'data-ce-min-width'],
            'iframe'    => ['height', 'width'],
        ],
    ],
];
