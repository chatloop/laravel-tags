# Add tags and taggable behaviour to a Laravel app

## Important Note:
This is a clone from [Spatie/laravel-tags](https://github.com/spatie/laravel-tags) to remove support for translations/locales intended for internal use.

## README
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package offers taggable behaviour for your models. After the package is installed the only thing you have to do is add the `HasTags` trait to an Eloquent model to make it taggable.

But we didn't stop with the regular tagging capabilities you find in every package. Laravel Tags comes with batteries included. Out of the box it has support for [multiple tag types](https://docs.spatie.be/laravel-tags/v4/advanced-usage/using-types) and [sorting capabilities](https://docs.spatie.be/laravel-tags/v4/advanced-usage/sorting-tags).

Here are some code examples:

```php
// apply HasTags trait to a model
use Illuminate\Database\Eloquent\Model;
use Chatloop\Tags\HasTags;

class NewsItem extends Model
{
    use HasTags;

    // ...
}
```

```php

// create a model with some tags
$newsItem = NewsItem::create([
   'name' => 'The Article Title',
   'tags' => ['first tag', 'second tag'], //tags will be created if they don't exist
]);

// attaching tags
$newsItem->attachTag('third tag');
$newsItem->attachTag('third tag','some_type');
$newsItem->attachTags(['fourth tag', 'fifth tag']);
$newsItem->attachTags(['fourth_tag','fifth_tag'],'some_type');

// detaching tags
$newsItem->detachTag('third tag');
$newsItem->detachTag('third tag','some_type');
$newsItem->detachTags(['fourth tag', 'fifth tag']);
$newsItem->detachTags(['fourth tag', 'fifth tag'],'some_type');

// get all tags of a model
$newsItem->tags;

// syncing tags
$newsItem->syncTags(['first tag', 'second tag']); // all other tags on this model will be detached

// syncing tags with a type
$newsItem->syncTagsWithType(['category 1', 'category 2'], 'categories');
$newsItem->syncTagsWithType(['topic 1', 'topic 2'], 'topics');

// retrieving tags with a type
$newsItem->tagsWithType('categories');
$newsItem->tagsWithType('topics');

// retrieving models that have any of the given tags
NewsItem::withAnyTags(['first tag', 'second tag'])->get();

// retrieve models that have all of the given tags
NewsItem::withAllTags(['first tag', 'second tag'])->get();

// retrieve models that don't have any of the given tags
NewsItem::withoutTags(['first tag', 'second tag'])->get();

// using tag types
$tag = Tag::findOrCreate('tag 1', 'my type');

// tags have slugs
$tag = Tag::findOrCreate('yet another tag');
$tag->slug; //returns "yet-another-tag"

// tags are sortable
$tag = Tag::findOrCreate('my tag');
$tag->order_column; //returns 1
$tag2 = Tag::findOrCreate('another tag');
$tag2->order_column; //returns 2

// manipulating the order of tags
$tag->swapOrder($anotherTag);

// checking if a model has a tag
$newsItem->hasTag('first tag');
$newsItem->hasTag('first tag', 'some_type');
```

## Installation

You can install the package via composer:

``` bash
composer require chatloop/laravel-tags
```

The package will automatically register itself.

You can publish the migration with:
```bash
php artisan vendor:publish --provider="Chatloop\Tags\TagsServiceProvider" --tag="tags-migrations"
```

After the migration has been published you can create the `tags` and `taggables` tables by running the migrations:

```bash
php artisan migrate
```

You can optionally publish the config file with:
```bash
php artisan vendor:publish --provider="Chatloop\Tags\TagsServiceProvider" --tag="tags-config"
```

## Credits
This is based on a clone from [Spatie/laravel-tags](https://github.com/spatie/laravel-tags) to remove support for translations/locales and other adjustments for Chatloop's use.
Credit goes to the original authors.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
