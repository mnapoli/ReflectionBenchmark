# Reflection benchmark

This is a benchmark of all methods allowing to access private properties (read and write).

We all know that micro-optimization is most of the time useless. However, if you write libraries or frameworks
making extensive use of reflection techniques, you probably wonder how to spare the impact of using reflection
to your users. This comparison is here to help by maintaining an up-to-date and reproducible summary.


## Methods

- [`Reflection` API](http://php.net/manual/en/book.reflection.php): read & write

```php
$reflectionProperty = new ReflectionProperty($object, $property);
$reflectionProperty->setAccessible(true);

$value = $reflectionProperty->getValue($object);
$reflectionProperty->setValue($object, 'new value');
```

- [Cast an object to an array](http://ocramius.github.io/blog/fast-php-object-to-array-conversion/): read

```php
$array = (array) $object;

$protectedKey = "\0*\0" . $this->propertyName;
$privateKey = "\0" . get_class($this->object) . "\0" . $this->propertyName;

if (array_key_exists($protectedKey, $array)) {
    $value = $array[$protectedKey];
} elseif (array_key_exists($privateKey, $array)) {
    $value = $array[$privateKey];
}
```

- [Bind a closure to the scope of an object](http://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/): read & write, but on PHP >= 5.4

```php
$get = Closure::bind(function($object, $prop) { return $object->{$prop}; }, null, $object);
$set = Closure::bind(function($object, $prop, $value) { $object->{$prop} = $value; }, null, $object);

$value = $get($object, $property);
$set($object, $property, 'new value');
```


## Running the benchmarks

Install the dependencies using Composer:

```sh
$ composer install
```

Run the benchmarks:

```sh
$ php -n vendor/bin/athletic -p benchmarks -b vendor/autoload.php
```

## Results

Those results were obtained on a development OS X machine using PHP 7.1.10. Feel free to run the benchmark
on your server and update these results if you get different ones. Be aware that "slightly faster" means that
it's probably not significant, whereas "faster" or "way faster" means that results are expected to be
reproducible for the same PHP version.

- Read a single property on *one* object:

The closure is slightly faster.

- Read a single property on *many* objects:

Reflection is way faster, the closure is almost as fast.

- Reading all the properties of an object:

The array cast is way faster, with the closure not far behind.

- Writing a single property on *one* object:

The closure is slightly faster.

- Writing a single property on *many* objects:

Reflection is slightly faster.

## Summary

**With PHP 7, if you were to use only one method for all operations then use the closure.** It is the solution that provides the best performances accross all operations.

### Raw results

```
Benchmark\ReadAllPropertiesOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [10,000    ] [0.0000030814409] [324,523.50188]
    arrayCast : [10,000    ] [0.0000008020163] [1,246,857.51657]
    closure   : [10,000    ] [0.0000010994911] [909,511.66623]


Benchmark\ReadSinglePropertyOnManyObjectsEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [100,000   ] [0.0000001455283] [6,871,514.93306]
    arrayCast : [100,000   ] [0.0000002697277] [3,707,442.63338]
    closure   : [100,000   ] [0.0000001647019] [6,071,573.94943]


Benchmark\ReadSinglePropertyOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [100,000   ] [0.0000004917526] [2,033,542.78179]
    arrayCast : [100,000   ] [0.0000004168963] [2,398,677.79182]
    closure   : [100,000   ] [0.0000004045892] [2,471,642.98721]


Benchmark\WriteSinglePropertyOnManyObjectsEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [50,000    ] [0.0000001499557] [6,668,633.93538]
    closure   : [50,000    ] [0.0000001540565] [6,491,122.94169]


Benchmark\WriteSinglePropertyOnOneObjectEvent
    Method Name   Iterations    Average Time      Ops/second
    ----------  ------------  --------------    -------------
    reflection: [50,000    ] [0.0000005018568] [1,992,600.26414]
    closure   : [50,000    ] [0.0000004431438] [2,256,603.61117]
```

## Contribute

Please provide fixes and improvements through pull requests.

Don't forget to update the results in the README.

## Credits

This is a compilation of the methods Marco Pivetta exposed on [his blog](http://ocramius.github.io/).

Read also [this article](https://ocramius.github.io/blog/accessing-private-php-class-members-without-reflection/).
