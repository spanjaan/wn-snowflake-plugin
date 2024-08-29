<?php
namespace SpAnjaan\Snowflake\Updates;

use SpAnjaan\Snowflake\Models\Type;
use Winter\Storm\Database\Updates\Seeder;

class addMediaTypes extends Seeder {

    public function run()
    {
        Type::create(['name' => 'mediaimage', 'id' => 11]);
        Type::create(['name' => 'mediafile', 'id' => 12]);
    }
}
