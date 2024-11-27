<?php

namespace Chatloop\Tags;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Tag extends Model implements Sortable
{
    use SortableTrait;
    use HasSlug;
    use HasFactory;

    public $guarded = [];

    public function scopeWithType(Builder $query, string $type = null, string $subtype = null): Builder
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type)
            ->where('subtype', $subtype)
            ->ordered();
    }

    public function scopeContaining(Builder $query, string $name): Builder
    {
        return $query->whereRaw("lower(name) LIKE ?", ['%' . mb_strtolower($name) . '%']);
    }

    public static function findOrCreate(
        string | array | ArrayAccess $values,
        string | null $type = null,
        string | null $subtype = null
    ): Collection | Tag | static {
        $tags = collect($values)->map(function ($value) use ($type, $subtype) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type, $subtype);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType(string $type, string $subtype = null): DbCollection
    {
        return static::withType($type, $subtype)->get();
    }

    public static function findFromString(string $name, string $type = null, string $subtype = null)
    {
        return static::query()
            ->where('type', $type)
            ->where('subtype', $subtype)
            ->where(function ($query) use ($name) {
                $query->where('name', $name)->orWhere('slug', $name);
            })
            ->first();
    }

    public static function findFromStringOfAnyType(string $name)
    {
        return static::query()
            ->where('name', $name)
            ->orWhere('slug', $name)
            ->get();
    }

    public static function findOrCreateFromString(string $name, string $type = null, string $subtype = null)
    {
        $tag = static::findFromString($name, $type, $subtype);

        if (! $tag) {
            $tag = static::create([
                'name' => $name,
                'type' => $type,
                'subtype' => $subtype,
            ]);
        }

        return $tag;
    }

    public static function getTypes(): Collection
    {
        return static::groupBy('type')->pluck('type');
    }
}
