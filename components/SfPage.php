<?php

namespace SpAnjaan\Snowflake\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page as CmsPage;
use SpAnjaan\Snowflake\Classes\EnumFieldType;
use SpAnjaan\Snowflake\Models\Layout;
use SpAnjaan\Snowflake\Models\Page;
use Winter\Storm\Support\Facades\Markdown;
use Winter\Storm\Support\Str;

class SfPage extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Snowflake',
            'description' => 'spanjaan.snowflake::lang.components.sf_page_desc',
            'icon' => 'icon-snowflake-o'
        ];
    }

    public function onRun()
    {
        $page = Page::where('filename', $this->page->baseFileName)->with('elements')->first();
        $layout = $this->page->layout->baseFileName;

        if (!$page) {
            return;
        }

        $layout = Layout::where('filename', $this->page->layout->baseFileName)->with('elements')->first();

        if ($layout) {
            $page->elements = $page->elements->merge($layout->elements);
        }

        foreach ($page->elements as $element) {
            switch ($element->type_id) {
                case EnumFieldType::Image:
                    if ($element->image) {
                        $path = $element->image->getPath();
                    } else {
                        $path = '';
                    }

                    $this->page[$element->cms_key] = $path;
                    $this->page[$element->cms_key . '__alt'] = $element->alt;

                    break;
                case EnumFieldType::Markdown:
                    $this->page[$element->cms_key] = Markdown::parse($element->content);

                    break;
                case EnumFieldType::File:
                    if ($element->file) {
                        $path = $element->file->getPath();
                    } else {
                        $path = '';
                    }

                    $this->page[$element->cms_key] = $path;
                    $this->page[$element->cms_key . '__name'] = $element->filename;

                    break;

                case EnumFieldType::MediaFile:
                    $path = media_path($element->content);

                    $this->page[$element->cms_key] = $path;
                    $this->page[$element->cms_key . '__name'] = $element->filename;

                    break;

                case EnumFieldType::MediaImage:
                    $path = media_path($element->content);

                    $this->page[$element->cms_key] = $path;
                    $this->page[$element->cms_key . '__alt'] = $element->alt;

                    break;

                default:
                    $this->page[$element->cms_key] = $element->content;
            }
        }
    }

}
