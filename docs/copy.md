# Forget/unset items by callback

With `forgetBy` you can delete items by using callback without creating new instance of collection like `reject()` does

```php
$collection = collect(['name' => 'Gen']);

$collection->copy('name', 'nameCopy'); 
Array
(
    [name] => Gen
    [nameCopy] => Gen
)

$collection->copy(['name' => 'nameCopy']); 
Array
(
    [name] => Gen
    [nameCopy] => Gen
)

$collection->copy(['name' => ['nameCopy', 'nameCopy2']]); 
Array
(
    [name] => Gen
    [nameCopy] => Gen
    [nameCopy2] => Gen
)


$collection->copy('notExistingKey', 'nameCopy'); 
Array
(
    [name] => Gen
    [nameCopy] => NULL
)

$collection->copy('notExistingKey', 'nameCopy','John doe'); 
Array
(
    [name] => Gen
    [nameCopy] => John doe
)
```