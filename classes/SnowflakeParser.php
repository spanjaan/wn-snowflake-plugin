<?php

namespace SpAnjaan\Snowflake\Classes;

use Illuminate\Support\Facades\DB;
use SpAnjaan\Snowflake\Classes\EnumFieldType;
use SpAnjaan\Snowflake\Models\Element;
use SpAnjaan\Snowflake\Models\Layout;
use SpAnjaan\Snowflake\Models\Page;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Str;

class SnowflakeParser
{
    /**
     * Parse Snowflake template.
     *
     * @param object $templateObject The template object containing markup.
     * @param string $objectType The type of object ('page', 'layout', 'partial').
     * @param bool $cleanup Whether to clean up unused records.
     * @return void
     * @throws ApplicationException If invalid tag or unsupported type is encountered.
     */
    public static function parseSnowflake(object $templateObject, string $objectType, bool $cleanup = false): void
    {
        $content = $templateObject->markup;

        $pattern = "/\{\{\s*(\w+)\s*.*sf\(([^|]*)\).*\}\}/";
        preg_match_all($pattern, $content, $matches);

        $tags = [];
        $sfAltKeys = ['__alt', '__name'];

        foreach ($matches[1] as $k => $v) {
            $paramString = $matches[2][$k] . ",";

            // Refactor empty arguments for correct parsing
            $paramString = str_replace(["''", '""'], ["' '", '" "'], $paramString);

            $pattern = "/['\"](.[^,]*)['\"]/";
            preg_match_all($pattern, $paramString, $submatches);
            $params = $submatches[1];

            $sfKey = $v;

            // Skip if Sf Key ends with __alt or __name
            if (Str::endsWith($sfKey, $sfAltKeys)) {
                continue;
            }

            if (empty($params) || strlen($sfKey) === 0) {
                throw new ApplicationException("Snowflake: invalid tag: {{ $sfKey }} (on page: " . $templateObject->getFilename() . ").");
            }

            $tags[$sfKey] = [
                'type' => $params[0] ?? '',
                'desc' => $params[2] ?? '',
                'default' => $params[1] ?? '',
            ];

            $ignoreDefault = ['image', 'file', 'date', 'mediaimage', 'mediafile'];
            if (!in_array($tags[$sfKey]['type'], $ignoreDefault)) {
                $tags[$sfKey]['default'] = $params[1] ?? '';
            }
        }

        self::syncDb($tags, $templateObject, $objectType, $cleanup);
    }

    /**
     * Synchronize Snowflake data with the database.
     *
     * @param array $tags The tags to sync.
     * @param object $templateObject The template object.
     * @param string $objectType The type of object ('page', 'layout', 'partial').
     * @param bool $cleanup Whether to clean up unused records.
     * @return void
     * @throws ApplicationException If unsupported type is encountered.
     */
    public static function syncDb(array $tags, object $templateObject, string $objectType, bool $cleanup): void
    {
        if (empty($tags)) {
            return;
        }

        $filename = $templateObject->getBaseFileName();
        $typesRaw = DB::table('spanjaan_snowflake_types')->get();

        $sfPage = null;
        $elements = [];

        switch ($objectType) {
            case 'page':
                $sfPage = DB::table('spanjaan_snowflake_pages')->where('filename', $filename)->first();
                if (!$sfPage) {
                    $sfPage = new Page();
                    $sfPage->filename = $filename;
                    $sfPage->save();
                }
                $elements = DB::table('spanjaan_snowflake_elements')->where('page_id', $sfPage->id)->get();
                break;

            case 'layout':
                $sfPage = DB::table('spanjaan_snowflake_layouts')->where('filename', $filename)->first();
                if (!$sfPage) {
                    $sfPage = new Layout();
                    $sfPage->filename = $filename;
                    $sfPage->save();
                }
                $elements = DB::table('spanjaan_snowflake_elements')->where('layout_id', $sfPage->id)->get();
                break;

            default:
                return;
        }

        $types = [];
        foreach ($typesRaw as $type) {
            $types[$type->name] = $type->id;
        }

        $dbArray = [];
        foreach ($elements as $element) {
            $dbArray[$element->cms_key] = [
                'type_id' => $element->type_id,
                'desc' => $element->desc,
                'id' => $element->id,
            ];

            // Clean up unused database records
            if (!isset($tags[$element->cms_key])) {
                $el = Element::find($dbArray[$element->cms_key]['id']);
                if (($el->type_id != EnumFieldType::Image && empty($el->content))
                    || ($el->type_id == EnumFieldType::Image && empty($el->image?->path))
                    || $cleanup) {
                    $el->delete();
                } else {
                    $el->in_use = 0;
                    $el->order = 9999;
                    $el->save();
                }
            }
        }

        $order = 1;
        foreach ($tags as $sfKey => $value) {
            if (!isset($types[$value['type']])) {
                throw new ApplicationException("Snowflake: type '{$value['type']}' is not supported (page: " . $templateObject->getFilename() . "). Supported types are: text, link, image, color, markdown, richeditor, code, date, textarea, file.");
            }

            if (isset($dbArray[$sfKey])) {
                // Update
                $el = Element::find($dbArray[$sfKey]['id']);
                $el->type_id = $types[$value['type']];
                $el->in_use = 1;
                $el->order = $order;
                $el->save();
            } else {
                // Insert
                $el = new Element();
                $el->type_id = $types[$value['type']];
                $el->order = $order;
                $el->desc = $value['desc'];
                $el->content = $value['default'];

                switch ($objectType) {
                    case 'page':
                        $el->page_id = $sfPage->id;
                        break;
                    case 'layout':
                        $el->layout_id = $sfPage->id;
                        break;
                }

                $el->cms_key = $sfKey;
                $el->save();
            }

            $order++;
        }
    }
}
