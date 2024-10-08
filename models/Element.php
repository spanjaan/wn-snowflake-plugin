<?php

namespace SpAnjaan\Snowflake\Models;

use Winter\Storm\Database\Model;
use SpAnjaan\Snowflake\Classes\EnumFieldType;

/**
 * element Model
 */
class Element extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];

    public $table = 'spanjaan_snowflake_elements';

    public $translatable = [
        'content',
        'alt',
    ];

    protected $fillable = ['*'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $rules = [];

    public $belongsTo = [
        'page' => 'SpAnjaan\Snowflake\Models\Page',
        'layout' => 'SpAnjaan\Snowflake\Models\Layout',
        'type' => 'SpAnjaan\Snowflake\Models\Type',
    ];

    public $attachOne = [
        'image' => 'System\Models\File',
        'file' => 'System\Models\File',
    ];


    public function scopeWithPage($query, $filtered)
    {
        return $query->whereHas('page', function ($q) use ($filtered) {
            $q->where('id', $filtered);
        });
    }

    public function scopeWithLayout($query, $filtered)
    {
        return $query->whereHas('layout', function ($q) use ($filtered) {
            $q->where('id', $filtered);
        });
    }

    public function beforeSave()
    {
        if ($this->type_id == EnumFieldType::Link) {
            $baseurl = rtrim(url('/'), '/');
            $this->content = str_replace($baseurl, '', $this->content);
        }
    }
}
