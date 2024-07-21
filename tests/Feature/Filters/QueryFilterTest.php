<?php

use Workbench\App\Models\Product;
use Illuminate\Support\Facades\DB;
use Conquest\Table\Filters\QueryFilter;
use Illuminate\Support\Facades\Request;
use Conquest\Table\Filters\Enums\Clause;
use Conquest\Table\Filters\Enums\Operator;
use Conquest\Table\Filters\Exceptions\QueryNotDefined;

it('can create a query filter', function () {
    $filter = new QueryFilter($n = 'name');
    expect($filter)
        ->getName()->toBe($n)
        ->getLabel()->toBe('Name')
        ->hasValidator()->toBeFalse()
        ->isAuthorised()->toBeTrue()
        ->hasTransform()->toBeFalse()
        ->hasMetadata()->toBeFalse();

    expect(fn() => $filter->getQuery())->toThrow(QueryNotDefined::class, 'Query for filter [{}] has not been provided.');
});

it('can create a filter with arguments', function () {
    $filter = new QueryFilter(
        name: 'name',
        label: 'Username',
        authorize: fn () => false,
        validator: fn () => true,
        transform: fn ($value) => $value,
        query: fn ($builder, $value) => $builder->where('name', $value),
        metadata: ['key' => 'value'],
    );

    expect($filter)->getName()->toBe('name')
        ->getLabel()->toBe('Username')
        ->isAuthorised()->toBeFalse()
        ->hasValidator()->toBeTrue()
        ->canTransform()->toBeTrue()
        ->hasMetadata()->toBeTrue();
    
    expect(fn() => $filter->getQuery())->not->toThrow(QueryNotDefined::class, 'Query for filter [{}] has not been provided.');
});

it('can make a filter', function () {
    expect($f = QueryFilter::make('name'))->toBeInstanceOf(QueryFilter::class)
        ->getName()->toBe('name')
        ->getLabel()->toBe('Name')
        ->hasValidator()->toBeFalse()
        ->isAuthorised()->toBeTrue()
        ->hasTransform()->toBeFalse()
        ->hasMetadata()->toBeFalse();
    
    expect(fn() => $f->getQuery())->toThrow(QueryNotDefined::class, 'Query for filter [{}] has not been provided.');
    
});

it('can chain methods on a filter', function () {
    $q = QueryFilter::make(
        name: 'name',
        label: 'Username',
        authorize: fn () => false,
        validator: fn () => true,
        transform: fn ($value) => $value,
        query: fn ($builder, $value) => $builder->where('name', $value),
        metadata: ['key' => 'value'],
    );
    expect($q)->getName()->toBe('name')
        ->getLabel()->toBe('Username')
        ->isAuthorised()->toBeFalse()
        ->hasValidator()->toBeTrue()
        ->canTransform()->toBeTrue()
        ->hasMetadata()->toBeTrue()
        ->getQuery()->toBeInstanceOf(Closure::class);
});

it('throws error when trying to apply query filter without a query', function () {
    $filter = QueryFilter::make('name');
    $builder = Product::query();
    Request::merge(['name' => 'test']);
    expect(fn () => $filter->apply($builder))->toThrow(QueryNotDefined::class);
});

it('can use a custom condition to apply the filter', function () {
    $filter = QueryFilter::make('id')
        ->query(fn ($builder, $value) => $builder->where('id', '>', $value))
        ->validate(fn ($value) => $value > 5);
    $builder = Product::query();
    Request::merge(['id' => 6]);
    expect(fn() => $filter->apply($builder))->not->toThrow(QueryNotDefined::class);
    expect($builder->toSql())->toBe('select * from "products" where "id" > ?'); 
});

it('can use a custom condition to prevent the filter', function () {
    $filter = QueryFilter::make('id')
        ->query(fn ($builder, $value) => $builder->where('id', '>', $value))
        ->validate(fn ($value) => $value > 5);
    $builder = Product::query();
    Request::merge(['id' => 5]);
    expect(fn() => $filter->apply($builder))->not->toThrow(QueryNotDefined::class);
    expect($builder->toSql())->toBe('select * from "products"'); 
});