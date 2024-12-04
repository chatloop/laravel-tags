<?php

namespace Chatloop\Tags;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::saving(function (Model $model) {
            $model->slug = $model->generateSlug();
        });
    }

    protected function generateSlug(): string
    {
        return static::slugify($this->name);
    }

    public static function slugify(string $name)
    {
        $slugger = config('tags.slugger');

        $slugger ??= '\Illuminate\Support\Str::slug';

        return call_user_func($slugger, $name);
    }
}
