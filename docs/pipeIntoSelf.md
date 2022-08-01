# Pipe into self

pipeIntoSelf() by itself isn't very useful, cause in case of \Illuminate\Support\Collection does the same
as `$collection->collect()`
<br > It becomes handy when custom Collections is used

```php
class MyAwesomeCollection extends \Illuminate\Support\Collection
{
    public function putOnSteroids(int $amount): static
    {
        $this->offsetSet('steroids', $amount);

        return $this;
    }
}

$regular = new MyAwesomeCollection([
    'name' => 'John Doe',
    'steroids' => 0
]);
$onSteroids = $regular->pipeIntoSelf()->putOnSteroids(10);
Array
(
    [name] => John Doe
    [steroids] => 10
)
//$regular hasn't change
Array
(
    [name] => John Doe
    [steroids] => 0
)
```

## Making fresh collection without pipeIntoSelf() extension

```php
//gives same result
$onSteroids = $regular->pipeInto(MyAwesomeCollection::class)->putOnSteroids(10);
```

## Use without `Collection::macro` in AppServiceProvider 
```php
class MyAwesomeCollection extends \Illuminate\Support\Collection
{
    use \Infira\Collection\extensions\PipeIntoSelf;

    public function putOnSteroids(int $amount): static
    {
        $this->offsetSet('steroids', $amount);
    }
}
```