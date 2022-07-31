# Dynamic method chain

Let's say that we have dynamically defined conditions in database, for example user pre saved filters,
and we want apply these conditions collections

```php
//example UsersFilters::get()->keyBy('method') which outputs
$userPreDefinedFilters = [
    'where' => [
        ['price', '>', 100],
        ['isActive', true]
    ],
];
```

And we have products

```php
$collection = collect([
    [
        'name' => 'Cheap product',
        'price' => 10,
        'isActive' => true
    ],
    [
        'name' => 'Expensive product',
        'price' => 10000,
        'isActive' => true,
    ],
    [
        'name' => 'Expensive product',
        'price' => 5600,
        'isActive' => false
    ]
]);
```

To filter products according to `$userPreDefinedFilters`

## Traditional laravel way

```php
$carry = $collection;
foreach ($userPreDefinedFilters as $method => $methodParams) {
    foreach ($methodParams as $params) {
        $carry = $carry->$method(...$params);
    }
}
if ($carry->containsOneItem()) {
    //do some stuff
}
```

## Using chain extension

```php
if ($collection->chain($userPreDefinedFilters)->containsOneItem()) {
    //do some stuff
}
```