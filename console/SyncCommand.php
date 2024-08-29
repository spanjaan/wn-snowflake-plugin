<?php

namespace SpAnjaan\Snowflake\Console;

use Cms\Classes\Layout;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Illuminate\Console\Command;
use SpAnjaan\Snowflake\Classes\SnowflakeParser;
use Symfony\Component\Console\Input\InputOption;

class SyncCommand extends Command
{
    protected $signature = 'snowflake:sync {--cleanup : Clean up unused Snowflake Keys}';
    protected $description = 'Scan pages and update Snowflake database.';

    public function handle()
    {
        if ($this->option('cleanup')) {
            $this->output->writeln('Cleaning up unused Snowflake Keys...');
        }

        $this->syncPages();
        $this->output->success('Snowflake sync complete');
    }

    protected function syncPages(): void
    {
        $active_theme = Theme::getActiveTheme();

        foreach (Page::listInTheme($active_theme) as $page) {
            $this->output->writeln('Syncing Snowflake Page: ' . $page->getFileName());

            SnowflakeParser::parseSnowflake($page, 'page', $this->option('cleanup'));
        }

        foreach (Layout::listInTheme($active_theme) as $layout) {
            $this->output->writeln('Syncing Snowflake Layout: ' . $layout->getFileName());

            SnowflakeParser::parseSnowflake($layout, 'layout', $this->option('cleanup'));
        }

    }
}
