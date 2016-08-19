# Optional

A port of Java 8's java.util.Optional improved for PHP.

## Some examples

### Basic usage

Example request class:
```php
class Request
{
    private $date;
    // ... constructor and getter ...
}
```

Example code of optional:
```php
$date = Optional::ofNullable($request->getDate())
    ->map(function ($dateString) {
        return DateTime::createFromFormat('Y-m-d', $dateString);
    })
    ->orElseGet(function () {
        return new \DateTime();
    });

echo $date->format('Y-m-d');
```

Given this request:
```php
$request = new Request('1992-10-07');
```

Program will output `1992-10-07`.

If a null is specified in request:
```php
$request = new Request(null);
```
Then program will output the default value, which in this case would be something like `2016-08-19`

Instead of returning a default value we could just throw an exception in case no value:

```php
$date = Optional::ofNullable($request->getDate())
    ->map(function ($dateString) {
        return DateTime::createFromFormat('Y-m-d', $dateString);
    })
    ->orElseThrow(new \Exception());
```

In case of:
```php
$request = new Request(null);
```
Program will throw the specified exception, otherwise, will return specified parsed date.

### Comparable

Returns true:
```php
Optional::of('something')->equals(Optional::of('something'));
```

Returns false:

```php
Optional::of(5)->equals(Optional::of('something'));
```

Returns true:
```php
Optional::empty()->equals(Optional::empty());
```

Returns false:
```php
Optional::empty()->equals(Optional::of(5));
```

### __toString

```php
echo (string) Optional::of('something');
```
Outputs: `Optional[something]`


```php
echo (string) Optional::empty();
```
Outputs: `Optional.empty`