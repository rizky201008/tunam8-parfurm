<?php

namespace App\Repository;

use App\Models\Tag;
use Illuminate\Support\Str;

class Tags
{
    protected $tags;

    public function __construct(Tag $tags = new Tag())
    {
        $this->tags = $tags;
    }

    public function createSlug($name)
    {
        return Str::slug(round(microtime(true) * 1000) . '-' . $name, '-');
    }
}
