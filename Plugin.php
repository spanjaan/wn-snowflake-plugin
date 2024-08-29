<?php namespace SpAnjaan\Snowflake;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;
use SpAnjaan\Snowflake\Classes\SnowflakeParser;
use SpAnjaan\Snowflake\Models\Settings;
use Winter\Storm\Support\Facades\Event;

/**
 * Snowflake Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'spanjaan.snowflake::lang.plugin.name',
            'description' => 'spanjaan.snowflake::lang.plugin.description',
            'author'      => 'SpAnjaan',
            'icon'        => 'icon-snowflake',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register(): void
    {
        Event::listen('cms.template.save', function ($controller, $templateObject, $type) {
            if (!in_array($type, ['page', 'layout', 'partial'], true)) {
                return;
            }
    
            SnowflakeParser::parseSnowflake($templateObject, $type);
        });
    
        $this->registerConsoleCommand('snowflake.sync', 'SpAnjaan\Snowflake\Console\SyncCommand');
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot(): void
    {
        // Optionally include boot logic here
    }

    /**
     * Registers any frontend components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents(): array
    {
        return [
            \SpAnjaan\Snowflake\Components\SfPage::class => 'sf_page',
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions(): array
    {
        return [
            'spanjaan.snowflake.use_snowflake' => [
                'tab'   => 'Snowflake',
                'label' => 'spanjaan.snowflake::lang.plugin.use_snowflake',
            ],
            'spanjaan.snowflake.manage_snowflake' => [
                'tab'   => 'Snowflake',
                'label' => 'spanjaan.snowflake::lang.plugin.manage_snowflake',
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation(): array
    {
        $label = Settings::get('custom_name') ?: 'Snowflake';

        return [
            'snowflake' => [
                'label'       => $label,
                'url'         => Backend::url('spanjaan/snowflake/pages'),
                'iconSvg'     => 'plugins/spanjaan/snowflake/assets/icons/snowflake-blue.svg',
                'permissions' => ['spanjaan.snowflake.*'],
                'order'       => 500,
                'sideMenu'    => [
                    'pages' => [
                        'label'       => 'spanjaan.snowflake::lang.models.page.label_plural',
                        'url'         => Backend::url('spanjaan/snowflake/pages'),
                        'icon'        => 'wn-icon-copy',
                        'permissions' => ['spanjaan.snowflake.*'],
                    ],
                    'layouts' => [
                        'label'       => 'spanjaan.snowflake::lang.models.layout.label_plural',
                        'url'         => Backend::url('spanjaan/snowflake/layouts'),
                        'icon'        => 'wn-icon-th-large',
                        'permissions' => ['spanjaan.snowflake.*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Registers settings for this plugin.
     *
     * @return array
     */
    public function registerSettings(): array
    {
        return [
            'snowflake' => [
                'label'       => 'spanjaan.snowflake::lang.plugin.name',
                'description' => 'spanjaan.snowflake::lang.plugin.manage_settings',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-snowflake',
                'class'       => 'SpAnjaan\Snowflake\Models\Settings',
                'order'       => 500,
                'keywords'    => 'snowflake',
                'permissions' => ['spanjaan.snowflake.manage_snowflake'],
            ],
        ];
    }

    /**
     * Registers markup tags for this plugin.
     *
     * @return array
     */
    public function registerMarkupTags(): array
    {
        return [
            'filters' => [
                'sf' => function ($cms_key) {
                    return $cms_key;
                },
            ],
        ];
    }
}
