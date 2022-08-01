# Forget/unset items by callback

With `forgetBy` you can delete items by using callback without creating new instance of collection like `reject()` does

```php
$collection = collect([1, 2, 3, 4]);

$collection->forgetBy(function ($value, $key) {
    return $value > 2;
});

$collection->all();

// [1, 2]
```