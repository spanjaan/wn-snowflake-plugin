<?php

return [
    'plugin' => [
        'name' => 'Snowflake',
        'description' => 'Dynamic Content Manager for Winter CMS',
        'manage_settings' => 'Manage Snowflake-related Settings',
    ],
    'permissions' => [
        'some_permission' => 'Some permission',
    ],
    'models' => [
        'general' => [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'error_no_page' => 'No Snowflake content found.',
        ],
        'settings' => [
            'custom_name' => 'Snowflake Menu Label',
            'markdown_mode' => 'Markdown Split Mode',
            'markdown_mode_comment' => 'Enable split mode for the Markdown editor to see live preview alongside editing.',
            'tab' => 'Snowflake',
            'section' => 'General Settings',
        ],
        'page' => [
            'label' => 'Page',
            'label_plural' => 'Pages',
        ],
        'layout' => [
            'label' => 'Layout',
            'label_plural' => 'Layouts',
        ],
    ],
    'component' => [
        'sfpage' => [
            'name' => 'Snowflake',
            'description' => 'Render the Snowflake in layouts, pages and partials.',
        ],
    ],
];
