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

### or
We can chain Optionality:

```php
$findInMemory = function () {
    ...
    return Optional::ofNullable(...);
};

$findInDisc = function () {
    ...
    return Optional::ofNullable(...);
};

$findRemotely = function () {
    ...
    return Optional::ofNullable(...);
};

$optional = $findInMemory()
    ->or($findInDisc)
    ->or($findRemotely);
```

Callbacks will only execute if the last optional is empty. Example:

```php
$findInMemory = function () {
    echo "Searching in memory...\n";
    return Optional::empty();
};

$findInDisc = function () {
    echo "Searching in disc...\n";
    return Optional::of("Awesome");
};

$findRemotely = function () {
    echo "Searching remotely...";
    return Optional::of("Not so awesome");
};

$findInMemory()
    ->or($findInDisc)
    ->or($findRemotely)
    ->ifPresent(function ($value) {
        echo $value;
    });
```

Will output:
```text
Searching in memory...
Searching in disc...
Awesome
```


### filter

```php
$request = new Request('1992-10-07');

$makeDateTime = function ($value) {
    return \DateTime::createFromFormat('Y-m-d', $value);
};

$beforeNow = function (\DateTime $date) {
    return $date->getTimestamp() > (new \DateTime())->getTimestamp();
};

$date = Optional::ofNullable($request->getDate())
    ->map($makeDateTime)
    ->filter($beforeNow)
    ->orElse(new \DateTime());

echo $date->format('Y-m-d');
```
Outputs (similar): `2016-08-19`

With: `$request = new Request('2030-01-01');` would output: `2030-01-01`

With: `$request = new Request(null);` would output: `2016-08-19`

### ifPresent

```php
Optional::ofNullable($request->getDate())
    ->ifPresent(function ($value) {
        echo "It's present: " . $value;
    });
```
If value, it would output: `It's present: 1992-10-07`
Otherwise would do nothing.

### ifPresentOrElse

Similar to ifPresent but with an else callback if optional is empty

```php
Optional::ofNullable($request->getDate())
    ->ifPresentOrElse(function ($value) {
        echo "It's present: " . $value;
    }, function() {
        echo 'No value is present';
    });
```
If value, it would output: `It's present: 1992-10-07`
Otherwise, it would output: `No value is present`

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