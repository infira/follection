# Use multiple key's with Collection->keyBy()

```php
$collection = collect([
    [
        'ID' => 10,
        'name' => 'Cheap product',
        'price' => 10,
        'isActive' => true
    ],
    [
        'ID' => 11,
        'name' => 'Expensive product',
        'price' => 10000,
        'isActive' => true,
    ],
    [
        'ID' => 11,
        'name' => 'Expensive product',
        'price' => 5600,
        'isActive' => false
    ]
]);
```

# Use dot as field separator
```php
$collection->keysBy('ID.name')->all();
//will output
Array
(
    [10.Cheap product] => Array
        (
            [ID] => 10
            [name] => Cheap product
            [price] => 10
            [isActive] => 1
        )

    [11.Expensive product] => Array
        (
            [ID] => 11
            [name] => Expensive product
            [price] => 5600
            [isActive] => 
        )

)
$collection->keysBy('ID.name','_')->all();
//will output
Array
(
    [10_Cheap product] => Array
        (
            [ID] => 10
            [name] => Cheap product
            [price] => 10
            [isActive] => 1
        )

    [11_Expensive product] => Array
        (
            [ID] => 11
            [name] => Expensive product
            [price] => 5600
            [isActive] => 
        )

)
```

# Use array
```php
$collection->keysBy(['ID', 'name'],'_s_')->all();
//will output
Array
(
    [10_s_Cheap product] => Array
        (
            [ID] => 10
            [name] => Cheap product
            [price] => 10
            [isActive] => 1
        )

    [11_s_Expensive product] => Array
        (
            [ID] => 11
            [name] => Expensive product
            [price] => 5600
            [isActive] => 
        )

)
```