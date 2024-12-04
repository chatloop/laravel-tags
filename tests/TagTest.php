<?php

use Chatloop\Tags\Tag;

beforeEach(function () {
    expect(Tag::all())->toHaveCount(0);
});

it('can create a tag', function () {
    $tag = Tag::findOrCreateFromString('string');

    expect(Tag::all())->toHaveCount(1);
    expect($tag->name)->toBe('string');
    expect($tag->type)->toBeNull();
});

it('creates sortable tags', function () {
    $tag = Tag::findOrCreateFromString('string');
    expect($tag->order_column)->toBe(1);

    $tag = Tag::findOrCreateFromString('string2');
    expect($tag->order_column)->toBe(2);
});

it('automatically generates a slug', function () {
    $tag = Tag::findOrCreateFromString('this is a tag');

    expect($tag->slug)->toBe('this-is-a-tag');
});


it('uses str slug if config slugger value is empty', function () {
    config()->set('tags.slugger', null);

    $tag = Tag::findOrCreateFromString('this is a tag');

    expect($tag->slug)->toBe('this-is-a-tag');
});


it('can use a custom slugger', function () {
    config()->set('tags.slugger', 'strtoupper');

    $tag = Tag::findOrCreateFromString('this is a tag');

    expect($tag->slug)->toBe('THIS IS A TAG');
});


it('can create a tag with a type', function () {
    $tag = Tag::findOrCreate('string', 'myType');

    expect($tag->type)->toBe('myType');
});

it('can create a tag with a subtype', function () {
    $tag = Tag::findOrCreate('string', 'myType', 'mySubtype');

    expect($tag->type)->toBe('myType');
    expect($tag->subtype)->toBe('mySubtype');
});


it('provides a scope to get all tags with a specific type', function () {
    Tag::findOrCreate('tagA', 'firstType');
    Tag::findOrCreate('tagB', 'firstType');
    Tag::findOrCreate('tagC', 'secondType');
    Tag::findOrCreate('tagD', 'secondType');

    expect(Tag::withType('firstType')->pluck('name')->toArray())->toMatchArray(['tagA', 'tagB']);
    expect(Tag::withType('secondType')->pluck('name')->toArray())->toMatchArray(['tagC', 'tagD']);
});

it('provides a scope to get all tags with a specific type and subtype', function () {
    Tag::findOrCreate('tagA', 'firstType', 'firstSubType');
    Tag::findOrCreate('tagB', 'firstType', 'secondSubType');
    Tag::findOrCreate('tagC', 'secondType', 'thirdSubType');
    Tag::findOrCreate('tagD', 'secondType', 'fourthSubType');
    Tag::findOrCreate('tagE', 'firstType', 'firstSubType');
    Tag::findOrCreate('tagF', 'secondType', 'thirdSubType');

    expect(Tag::withType('firstType', 'firstSubType')->pluck('name')->toArray())->toMatchArray(['tagA', 'tagE']);
    expect(Tag::withType('secondType', 'thirdSubType')->pluck('name')->toArray())->toMatchArray(['tagC', 'tagF']);
});


it('provides a scope to get all tags the contain a certain string', function () {
    Tag::findOrCreate('one');
    Tag::findOrCreate('another-one');
    Tag::findOrCreate('another-ONE-with-different-casing');
    Tag::findOrCreate('two');

    expect(Tag::containing('on')->pluck('name')->toArray())->toMatchArray([
        'one',
        'another-one',
        'another-ONE-with-different-casing',
    ]);
    expect(Tag::containing('tw')->pluck('name')->toArray())->toMatchArray(['two']);
});


it('provides a method to get all tags with a specific type', function () {
    Tag::findOrCreate('tagA', 'firstType');
    Tag::findOrCreate('tagB', 'firstType');
    Tag::findOrCreate('tagC', 'secondType');
    Tag::findOrCreate('tagD', 'secondType');

    expect(Tag::getWithType('firstType')->pluck('name')->toArray())->toMatchArray(['tagA', 'tagB']);
    expect(Tag::getWithType('secondType')->pluck('name')->toArray())->toMatchArray(['tagC', 'tagD']);
});

it('provides a method to get all tags with a specific type and subtype', function () {
    Tag::findOrCreate('tagA', 'firstType', 'firstSubType');
    Tag::findOrCreate('tagB', 'firstType', 'secondSubType');
    Tag::findOrCreate('tagC', 'secondType', 'thirdSubType');
    Tag::findOrCreate('tagD', 'secondType', 'fourthSubType');
    Tag::findOrCreate('tagE', 'firstType', 'firstSubType');
    Tag::findOrCreate('tagF', 'secondType', 'thirdSubType');

    expect(Tag::getWithType('firstType', 'firstSubType')->pluck('name')->toArray())->toMatchArray(['tagA', 'tagE']);
    expect(Tag::getWithType('secondType', 'thirdSubType')->pluck('name')->toArray())->toMatchArray(['tagC', 'tagF']);
});


it('will not create a tag if the tag already exists', function () {
    Tag::findOrCreate('string');

    Tag::findOrCreate('string');

    expect(Tag::all())->toHaveCount(1);
});

it('will not create a tag if the tag already exists with same slug', function () {
    Tag::findOrCreate('another string');

    Tag::findOrCreate('Another-string');

    expect(Tag::all())->toHaveCount(1);
});

it('will not create a tag if the tag already exists with same type', function () {
    Tag::findOrCreate('string', 'myType');

    Tag::findOrCreate('string', 'myType');

    expect(Tag::all())->toHaveCount(1);
});


it('will create a tag if a tag exists with the same name but a different type', function () {
    Tag::findOrCreate('string');

    Tag::findOrCreate('string', 'myType');

    expect(Tag::all())->toHaveCount(2);
});

it('will create a tag if a tag exists with the same name and type but a different subtype', function () {
    Tag::findOrCreate('string', 'myType');

    Tag::findOrCreate('string', 'myType', 'mySubtype');

    Tag::findOrCreate('string', 'myType', 'anotherSubType');

    expect(Tag::all())->toHaveCount(3);
});


it('can create tags using an array', function () {
    Tag::findOrCreate(['tag1', 'tag2', 'tag3']);

    expect(Tag::all())->toHaveCount(3);
});


it('can create tags using a collection', function () {
    Tag::findOrCreate(collect(['tag1', 'tag2', 'tag3']));

    expect(Tag::all())->toHaveCount(3);
});


it('can find or create a tag', function () {
    $tag = Tag::findOrCreate('string');

    $tag2 = Tag::findOrCreate($tag->name);

    expect($tag2->name)->toBe('string');
});


it('can find tags from a string with any type', function () {
    Tag::findOrCreate('tag1');

    Tag::findOrCreate('tag1', 'myType1');

    Tag::findOrCreate('tag1', 'myType2');

    Tag::findOrCreate('tag1', 'myType1', 'subType');

    $tags = Tag::findFromStringOfAnyType('tag1');

    expect($tags)->toHaveCount(4);
});


it('name can be changed by setting its name property to a new value', function () {
    $tag = Tag::findOrCreate('my tag');

    $tag->name = 'new name';

    $tag->save();

    expect($tag->name)->toBe('new name');
});


it('gets all tag types', function () {
    Tag::findOrCreate('foo', 'type1');
    Tag::findOrCreate('bar', 'type1');
    Tag::findOrCreate('baz', 'type2');
    Tag::findOrCreate('qux', 'type2');

    $types = Tag::getTypes();

    expect($types)->toHaveCount(2);
    expect($types[0])->toBe('type1');
    expect($types[1])->toBe('type2');
});
